<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class GeocodingService
{
    /**
     * Resolve geographic coordinates from a city, port, or address query via OpenStreetMap Nominatim.
     */
    public function searchLocation(string $query, int $limit = 5): array
    {
        $cleanQuery = trim($query);
        if ($cleanQuery === '') {
            return [
                'available' => false,
                'status' => 'EMPTY_QUERY',
                'results' => [],
                'message' => 'Kata kunci lokasi tidak boleh kosong.',
            ];
        }

        $url = config('services.geocoding.base_url', 'https://nominatim.openstreetmap.org/search');
        $userAgent = config('services.geocoding.user_agent', 'CargoTraceLogisticsPlatform/1.0');
        $timeout = (int) config('services.geocoding.timeout', 5);

        try {
            $params = [
                'q' => $cleanQuery,
                'format' => 'json',
                'limit' => $limit,
                'addressdetails' => 1,
            ];

            if (config('services.geocoding.api_key')) {
                $params['key'] = config('services.geocoding.api_key');
            }

            $response = Http::timeout($timeout)
                ->retry(2, 150, throw: false)
                ->withHeaders([
                    'User-Agent' => $userAgent,
                    'Accept' => 'application/json',
                ])
                ->get($url, $params);

            if (! $response->successful()) {
                Log::warning('Geocoding API returned error response', [
                    'status' => $response->status(),
                    'query' => $cleanQuery,
                ]);

                return [
                    'available' => false,
                    'status' => 'API_ERROR',
                    'http_status' => $response->status(),
                    'results' => [],
                    'message' => "Layanan geocoding mengembalikan HTTP {$response->status()}.",
                ];
            }

            $data = $response->json();
            if (! is_array($data) || empty($data)) {
                return [
                    'available' => true,
                    'status' => 'NO_RESULTS',
                    'results' => [],
                    'message' => "Tidak ditemukan koordinat untuk '{$cleanQuery}'.",
                ];
            }

            $normalized = collect($data)->map(function ($item) {
                $address = $item['address'] ?? [];
                return [
                    'display_name' => $item['display_name'] ?? 'Unknown Location',
                    'latitude' => isset($item['lat']) ? round((float) $item['lat'], 6) : null,
                    'longitude' => isset($item['lon']) ? round((float) $item['lon'], 6) : null,
                    'city' => $address['city'] ?? $address['town'] ?? $address['state'] ?? '',
                    'country' => $address['country'] ?? '',
                    'type' => $item['type'] ?? 'location',
                ];
            })->filter(fn ($r) => $r['latitude'] !== null && $r['longitude'] !== null)->values()->all();

            if (empty($normalized)) {
                return [
                    'available' => true,
                    'status' => 'NO_RESULTS',
                    'results' => [],
                    'message' => 'Data koordinat kosong dari penyedia geocoding.',
                ];
            }

            return [
                'available' => true,
                'status' => 'OK',
                'provider' => 'OpenStreetMap Nominatim',
                'results' => $normalized,
                'message' => count($normalized) . ' lokasi ditemukan.',
            ];
        } catch (ConnectionException $e) {
            Log::warning('Geocoding API timeout/connection error', [
                'query' => $cleanQuery,
                'error' => $e->getMessage(),
            ]);

            return [
                'available' => false,
                'status' => 'TIMEOUT_OR_NETWORK_ERROR',
                'results' => [],
                'message' => 'Layanan geocoding timeout atau tidak dapat dihubungi.',
            ];
        } catch (Throwable $e) {
            Log::error('Unexpected Geocoding API failure', [
                'query' => $cleanQuery,
                'error' => $e->getMessage(),
            ]);

            return [
                'available' => false,
                'status' => 'UNEXPECTED_ERROR',
                'results' => [],
                'message' => 'Gagal memproses pencarian koordinat.',
            ];
        }
    }
}
