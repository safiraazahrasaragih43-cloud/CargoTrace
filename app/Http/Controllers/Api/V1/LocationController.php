<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Location\StoreLocationRequest;
use App\Http\Requests\Location\UpdateLocationRequest;
use App\Http\Resources\LocationResource;
use App\Models\Location;
use App\Services\LocationService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected LocationService $locationService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Location::class);

        $locations = $this->locationService->getLocations(
            $request->only(['search', 'type']),
            $request->integer('per_page', 15)
        );

        return $this->successResponse([
            'items' => LocationResource::collection($locations->items()),
            'pagination' => [
                'current_page' => $locations->currentPage(),
                'last_page' => $locations->lastPage(),
                'per_page' => $locations->perPage(),
                'total' => $locations->total(),
            ],
        ], 'Locations retrieved successfully');
    }

    public function store(StoreLocationRequest $request): JsonResponse
    {
        $location = $this->locationService->createLocation($request->validated());

        return $this->successResponse(
            new LocationResource($location),
            'Location created successfully',
            201
        );
    }

    public function show(Location $location): JsonResponse
    {
        $this->authorize('view', $location);

        return $this->successResponse(
            new LocationResource($location),
            'Location retrieved successfully'
        );
    }

    public function update(UpdateLocationRequest $request, Location $location): JsonResponse
    {
        $updated = $this->locationService->updateLocation($location, $request->validated());

        return $this->successResponse(
            new LocationResource($updated),
            'Location updated successfully'
        );
    }

    public function destroy(Location $location): JsonResponse
    {
        $this->authorize('delete', $location);

        $this->locationService->deleteLocation($location);

        return $this->successResponse(null, 'Location deleted successfully');
    }
}
