<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Exception\StoreExceptionRequest;
use App\Http\Requests\Exception\UpdateExceptionRequest;
use App\Http\Resources\ShipmentExceptionResource;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Services\ExceptionService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;

class ExceptionController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected ExceptionService $exceptionService
    ) {}

    /**
     * List all operational exceptions for a specific shipment.
     */
    public function index(Shipment $shipment): JsonResponse
    {
        $this->authorize('viewAny', [ShipmentException::class, $shipment]);

        $exceptions = $this->exceptionService->getShipmentExceptions($shipment);

        return $this->successResponse(
            ShipmentExceptionResource::collection($exceptions),
            'Shipment exceptions retrieved successfully'
        );
    }

    /**
     * Report a new exception on an active shipment.
     */
    public function store(StoreExceptionRequest $request, Shipment $shipment): JsonResponse
    {
        $this->authorize('create', [ShipmentException::class, $shipment]);

        $exception = $this->exceptionService->reportException(
            $shipment,
            $request->validated(),
            $request->user()
        );

        return $this->successResponse(
            new ShipmentExceptionResource($exception),
            'Shipment exception reported successfully',
            201
        );
    }

    /**
     * Update or resolve an existing shipment exception.
     */
    public function update(UpdateExceptionRequest $request, ShipmentException $exception): JsonResponse
    {
        $this->authorize('update', $exception);

        $updated = $this->exceptionService->updateException(
            $exception,
            $request->validated(),
            $request->user()
        );

        return $this->successResponse(
            new ShipmentExceptionResource($updated),
            'Shipment exception updated successfully'
        );
    }
}

