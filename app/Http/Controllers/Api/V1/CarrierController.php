<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Carrier\StoreCarrierRequest;
use App\Http\Requests\Carrier\UpdateCarrierRequest;
use App\Http\Resources\CarrierResource;
use App\Models\Carrier;
use App\Services\CarrierService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CarrierController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected CarrierService $carrierService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Carrier::class);

        $carriers = $this->carrierService->getCarriers(
            $request->only(['search', 'is_active']),
            $request->integer('per_page', 15)
        );

        return $this->successResponse([
            'items' => CarrierResource::collection($carriers->items()),
            'pagination' => [
                'current_page' => $carriers->currentPage(),
                'last_page' => $carriers->lastPage(),
                'per_page' => $carriers->perPage(),
                'total' => $carriers->total(),
            ],
        ], 'Carriers retrieved successfully');
    }

    public function store(StoreCarrierRequest $request): JsonResponse
    {
        $carrier = $this->carrierService->createCarrier($request->validated());

        return $this->successResponse(
            new CarrierResource($carrier),
            'Carrier created successfully',
            201
        );
    }

    public function show(Carrier $carrier): JsonResponse
    {
        $this->authorize('view', $carrier);

        return $this->successResponse(
            new CarrierResource($carrier),
            'Carrier retrieved successfully'
        );
    }

    public function update(UpdateCarrierRequest $request, Carrier $carrier): JsonResponse
    {
        $updated = $this->carrierService->updateCarrier($carrier, $request->validated());

        return $this->successResponse(
            new CarrierResource($updated),
            'Carrier updated successfully'
        );
    }

    public function destroy(Carrier $carrier): JsonResponse
    {
        $this->authorize('delete', $carrier);

        $this->carrierService->deleteCarrier($carrier);

        return $this->successResponse(null, 'Carrier deleted successfully');
    }
}
