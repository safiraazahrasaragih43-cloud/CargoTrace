<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shipment\StoreShipmentRequest;
use App\Http\Requests\Shipment\UpdateEtaRequest;
use App\Http\Requests\Shipment\UpdateShipmentRequest;
use App\Http\Resources\ShipmentResource;
use App\Models\Shipment;
use App\Services\EtaService;
use App\Services\ShipmentService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected ShipmentService $shipmentService,
        protected EtaService $etaService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Shipment::class);

        $shipments = $this->shipmentService->getShipments(
            $request->user(),
            $request->only(['search', 'status', 'carrier_id', 'customer_id', 'is_delayed']),
            $request->integer('per_page', 15)
        );

        return $this->successResponse([
            'items' => ShipmentResource::collection($shipments->items()),
            'pagination' => [
                'current_page' => $shipments->currentPage(),
                'last_page' => $shipments->lastPage(),
                'per_page' => $shipments->perPage(),
                'total' => $shipments->total(),
            ],
        ], 'Shipments retrieved successfully');
    }

    public function store(StoreShipmentRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $items = $validated['items'] ?? null;
        unset($validated['items']);

        $shipment = $this->shipmentService->createShipment($validated, $items);

        return $this->successResponse(
            new ShipmentResource($shipment),
            'Shipment created successfully',
            201
        );
    }

    public function show(Request $request, Shipment $shipment): JsonResponse
    {
        $this->authorize('view', $shipment);

        $loaded = $shipment->load(['customer', 'carrier', 'origin', 'destination', 'currentLocation', 'items', 'exceptions']);

        return $this->successResponse(
            new ShipmentResource($loaded),
            'Shipment retrieved successfully'
        );
    }

    public function update(UpdateShipmentRequest $request, Shipment $shipment): JsonResponse
    {
        $updated = $this->shipmentService->updateShipment($shipment, $request->validated());

        return $this->successResponse(
            new ShipmentResource($updated),
            'Shipment updated successfully'
        );
    }

    public function destroy(Shipment $shipment): JsonResponse
    {
        $this->authorize('delete', $shipment);

        $this->shipmentService->deleteShipment($shipment);

        return $this->successResponse(null, 'Shipment deleted successfully');
    }

    /**
     * Evaluate deterministic ETA & delay rules for a shipment.
     */
    public function evaluateEta(Shipment $shipment): JsonResponse
    {
        $this->authorize('view', $shipment);

        $evaluation = $this->etaService->evaluateShipment($shipment);

        return $this->successResponse($evaluation, 'ETA evaluation completed');
    }

    /**
     * Update estimated arrival date and recalculate deterministic delay metrics.
     */
    public function updateEta(UpdateEtaRequest $request, Shipment $shipment): JsonResponse
    {
        $this->authorize('update', $shipment);

        $evaluation = $this->etaService->recalculateEta(
            $shipment,
            $request->validated('estimated_delivery_at'),
            $request->validated('reason')
        );

        return $this->successResponse([
            'evaluation' => $evaluation,
            'shipment' => new ShipmentResource($shipment->fresh(['origin', 'destination', 'carrier'])),
        ], 'ETA updated and delay rules recalculated');
    }

    /**
     * Retrieve global combined operational KPIs across all corridors.
     */
    public function kpis(): JsonResponse
    {
        $kpis = $this->etaService->calculateGlobalKpis();

        return $this->successResponse($kpis, 'Global operational KPIs retrieved');
    }

    /**
     * Public tracking endpoint (accessible without authentication).
     */
    public function track(string $trackingNumber): JsonResponse
    {
        $shipment = $this->shipmentService->findByTrackingNumber($trackingNumber);
        $shipment->load(['customer', 'carrier', 'origin', 'destination', 'currentLocation', 'items', 'trackingEvents.location', 'exceptions']);

        return $this->successResponse(
            new ShipmentResource($shipment),
            'Shipment tracking details retrieved'
        );
    }
}
