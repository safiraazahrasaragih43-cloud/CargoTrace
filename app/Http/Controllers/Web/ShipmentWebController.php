<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tracking\StoreTrackingEventRequest;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\TrackingEvent;
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

