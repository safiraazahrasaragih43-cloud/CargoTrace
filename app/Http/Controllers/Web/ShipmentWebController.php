<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shipment\StoreShipmentRequest;
use App\Http\Requests\Tracking\StoreTrackingEventRequest;
use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\TrackingEvent;
use App\Models\User;
use App\Services\ShipmentService;
use App\Services\TrackingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShipmentWebController extends Controller
{
    public function __construct(
        protected ShipmentService $shipmentService,
        protected TrackingService $trackingService
    ) {}

    /**
     * Display the dedicated Shipments Manifest management page.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Shipment::class);

        $shipments = $this->shipmentService->getShipments(
            $request->user(),
            $request->only(['search', 'status', 'carrier_id']),
            12
        );

        return view('shipments.index', [
            'shipments' => $shipments,
            'filters' => $request->only(['search', 'status', 'carrier_id']),
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
            ->with('status', "Shipment {$shipment->tracking_number} created successfully.");
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
     * Display the shipment detail and tracking timeline page.
     */
    public function show(Request $request, string $trackingNumber): View
    {
        $shipment = $this->shipmentService->findByTrackingNumber($trackingNumber);

        // RBAC: If authenticated as customer, verify shipment ownership
        if ($request->user() && $request->user()->isCustomer()) {
            if ($request->user()->id !== $shipment->customer_id) {
                abort(403, 'Unauthorized access to consignment data.');
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
        ]);

        $locations = Location::orderBy('name')->get();

        return view('shipments.show', [
            'shipment' => $shipment,
            'timeline' => $shipment->trackingEvents,
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
            ->with('status', 'Tracking event successfully logged into immutable ledger.');
    }
}
