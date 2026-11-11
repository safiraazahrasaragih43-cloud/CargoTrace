<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Exception\StoreExceptionRequest;
use App\Http\Requests\Exception\UpdateExceptionRequest;
use App\Http\Requests\Shipment\StoreShipmentRequest;
use App\Http\Requests\Shipment\UpdateEtaRequest;
use App\Http\Requests\Tracking\StoreTrackingEventRequest;
use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\TrackingEvent;
use App\Models\User;
use App\Services\EtaService;
use App\Services\ExceptionService;
use App\Services\ShipmentService;
use App\Services\TrackingService;
use App\Services\WeatherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShipmentWebController extends Controller
{
    public function __construct(
        protected ShipmentService $shipmentService,
        protected TrackingService $trackingService,
        protected EtaService $etaService,
        protected ExceptionService $exceptionService,
        protected WeatherService $weatherService
    ) {}

    /**
     * Display the dedicated Shipments Manifest management page.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Shipment::class);

        $filterKeys = ['search', 'status', 'carrier_id', 'origin_id', 'destination_id', 'is_delayed', 'date_from', 'date_to', 'sort_by'];

        $shipments = $this->shipmentService->getShipments(
            $request->user(),
            $request->only($filterKeys),
            12
        );

        return view('shipments.index', [
            'shipments' => $shipments,
            'filters' => $request->only($filterKeys),
            'customers' => User::where('role', User::ROLE_CUSTOMER)->orderBy('name')->get(),
            'carriers' => Carrier::where('is_active', true)->orderBy('name')->get(),
            'locations' => Location::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a new shipment from the web manifest console.
     */
    public function store(StoreShipmentRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $items = $validated['items'] ?? null;
        unset($validated['items']);

        $shipment = $this->shipmentService->createShipment($validated, $items);

        return redirect()
            ->route('shipments.show', $shipment->tracking_number)
            ->with('status', "Pengiriman {$shipment->tracking_number} berhasil dibuat.");
    }

    /**
     * Display the dedicated Live Quick Track lookup page.
     */
    public function trackPage(Request $request): View
    {
        $recentShipments = Shipment::with(['origin', 'destination'])
            ->latest()
            ->take(3)
            ->get();

        return view('tracking.index', [
            'initialQuery' => $request->query('tracking_number', ''),
            'recentShipments' => $recentShipments,
        ]);
    }

    /**
     * Display the shipment detail, ETA rule evaluation, exceptions, and tracking timeline page.
     */
    public function show(Request $request, string $trackingNumber): View
    {
        $shipment = $this->shipmentService->findByTrackingNumber($trackingNumber);

        // RBAC: If authenticated as customer, verify shipment ownership
        if ($request->user() && $request->user()->isCustomer()) {
            if ($request->user()->id !== $shipment->customer_id) {
                abort(403, 'Akses tidak diizinkan untuk melihat data pengiriman ini.');
            }
        }

        $shipment->load([
            'customer',
            'carrier',
            'origin',
            'destination',
            'currentLocation',
            'items',
            'trackingEvents.location',
            'trackingEvents.creator',
            'exceptions.reporter',
            'exceptions.resolver',
        ]);

        $etaEvaluation = $this->etaService->evaluateShipment($shipment);
        $locations = Location::orderBy('name')->get();

        return view('shipments.show', [
            'shipment' => $shipment->fresh([
                'customer',
                'carrier',
                'origin',
                'destination',
                'currentLocation',
                'items',
                'trackingEvents.location',
                'trackingEvents.creator',
                'exceptions.reporter',
                'exceptions.resolver',
            ]),
            'timeline' => $shipment->trackingEvents,
            'exceptions' => $shipment->exceptions,
            'etaEvaluation' => $etaEvaluation,
            'locations' => $locations,
            'user' => $request->user(),
        ]);
    }

    /**
     * Store a new tracking event checkpoint from the web interface.
     */
    public function storeEvent(StoreTrackingEventRequest $request, string $trackingNumber): RedirectResponse
    {
        $shipment = $this->shipmentService->findByTrackingNumber($trackingNumber);

        $this->authorize('create', [TrackingEvent::class, $shipment]);

        $this->trackingService->addEvent(
            $shipment,
            $request->validated(),
            $request->user()
        );

        return redirect()
            ->route('shipments.show', $shipment->tracking_number)
            ->with('status', 'Titik checkpoint pelacakan berhasil dicatat.');
    }

    /**
     * Report a shipment exception from the web detail page.
     */
    public function storeException(StoreExceptionRequest $request, string $trackingNumber): RedirectResponse
    {
        $shipment = $this->shipmentService->findByTrackingNumber($trackingNumber);

        $this->authorize('create', [ShipmentException::class, $shipment]);

        $this->exceptionService->reportException(
            $shipment,
            $request->validated(),
            $request->user()
        );

        return redirect()
            ->route('shipments.show', $shipment->tracking_number)
            ->with('status', 'Kendala pengiriman (Exception) berhasil dilaporkan dan status diperbarui.');
    }

    /**
     * Update or resolve a shipment exception from the web detail page.
     */
    public function updateException(UpdateExceptionRequest $request, ShipmentException $exception): RedirectResponse
    {
        $this->authorize('update', $exception);

        $updated = $this->exceptionService->updateException(
            $exception,
            $request->validated(),
            $request->user()
        );

        return redirect()
            ->route('shipments.show', $updated->shipment->tracking_number)
            ->with('status', "Status kendala ({$updated->type}) diperbarui menjadi {$updated->status}.");
    }

    /**
     * Recalculate ETA from web modal.
     */
    public function updateEta(UpdateEtaRequest $request, string $trackingNumber): RedirectResponse
    {
        $shipment = $this->shipmentService->findByTrackingNumber($trackingNumber);

        $this->authorize('update', $shipment);

        $this->etaService->recalculateEta(
            $shipment,
            $request->validated('estimated_delivery_at'),
            $request->validated('reason')
        );

        return redirect()
            ->route('shipments.show', $shipment->tracking_number)
            ->with('status', 'Estimasi waktu kedatangan (ETA) dan evaluasi keterlambatan berhasil diperbarui.');
    }

    /**
     * Fetch live weather conditions for a shipment's current location or destination port (JSON).
     */
    public function weather(Request $request, string $trackingNumber): JsonResponse
    {
        $shipment = $this->shipmentService->findByTrackingNumber($trackingNumber);

        if ($request->user() && $request->user()->isCustomer() && $request->user()->id !== $shipment->customer_id) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $shipment->loadMissing(['currentLocation', 'destination', 'origin', 'latestTrackingEvent']);
        $latestEvent = $shipment->latestTrackingEvent;

        $lat = $latestEvent?->latitude ?? $shipment->currentLocation?->latitude ?? $shipment->destination?->latitude;
        $lng = $latestEvent?->longitude ?? $shipment->currentLocation?->longitude ?? $shipment->destination?->longitude;
        $label = $latestEvent?->location_name ?? $shipment->currentLocation?->name ?? $shipment->destination?->name ?? 'Lokasi Pengiriman';

        return response()->json([
            'success' => true,
            'data' => $this->weatherService->getWeatherForCoordinates($lat, $lng, $label),
        ]);
    }
}
