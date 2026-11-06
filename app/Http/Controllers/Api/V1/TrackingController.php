<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tracking\StoreTrackingEventRequest;
use App\Http\Resources\TrackingEventResource;
use App\Models\Shipment;
use App\Models\TrackingEvent;
use App\Services\TrackingService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected TrackingService $trackingService
    ) {}

    /**
     * Get the chronological tracking timeline for a shipment.
     */
    public function index(Request $request, Shipment $shipment): JsonResponse
    {
        $this->authorize('view', [TrackingEvent::class, $shipment]);

        $timeline = $this->trackingService->getTimeline($shipment);

        return $this->successResponse(
            TrackingEventResource::collection($timeline),
            'Tracking timeline retrieved successfully'
        );
    }

    /**
     * Add a new tracking checkpoint event to a shipment.
     */
    public function store(StoreTrackingEventRequest $request, Shipment $shipment): JsonResponse
    {
        $this->authorize('create', [TrackingEvent::class, $shipment]);

        $event = $this->trackingService->addEvent(
            $shipment,
            $request->validated(),
            $request->user()
        );

        return $this->successResponse(
            new TrackingEventResource($event),
            'Tracking event recorded successfully',
            201
        );
    }
}
