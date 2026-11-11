<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Services\GeocodingService;
use App\Services\WeatherService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IntegrationController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected WeatherService $weatherService,
        protected GeocodingService $geocodingService
    ) {}

    /**
     * Get live weather conditions at the shipment's latest position or destination port.
     */
    public function shipmentWeather(Shipment $shipment): JsonResponse
    {
        $this->authorize('view', $shipment);

        $shipment->loadMissing(['currentLocation', 'destination', 'origin', 'latestTrackingEvent']);

        $latestEvent = $shipment->latestTrackingEvent;
        $lat = $latestEvent?->latitude ?? $shipment->currentLocation?->latitude ?? $shipment->destination?->latitude;
        $lng = $latestEvent?->longitude ?? $shipment->currentLocation?->longitude ?? $shipment->destination?->longitude;
        $label = $latestEvent?->location_name ?? $shipment->currentLocation?->name ?? $shipment->destination?->name ?? 'Lokasi Pengiriman';

        $weather = $this->weatherService->getWeatherForCoordinates($lat, $lng, $label);

        return $this->successResponse($weather, 'Weather context retrieved');
    }

    /**
     * Geocode a city or port query using Nominatim OpenStreetMap API.
     */
    public function geocode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => ['required', 'string', 'min:2', 'max:200'],
        ]);

        $result = $this->geocodingService->searchLocation($validated['q'], 5);

        return $this->successResponse($result, $result['message'] ?? 'Geocoding query completed');
    }
}

