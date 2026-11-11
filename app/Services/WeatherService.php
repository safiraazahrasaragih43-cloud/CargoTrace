<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class WeatherService
{
    public const DISCLAIMER = 'Kondisi cuaca saat ini hanya sebagai konteks visibilitas operasional dan bukan klaim bukti prediksi keterlambatan pengiriman.';

    /**
     * Fetch current weather conditions for given GPS coordinates with resilient error/timeout handling.
     */
    public function getWeatherForCoordinates(?float $latitude, ?float $longitude, string $locationLabel = ''): array
    {
        if ($latitude === null || $longitude === null) {
            return [
                'available' => false,
                'status' => 'NO_COORDINATES',
                'location' => $locationLabel,
                'message' => 'Koordinat GPS belum tersedia untuk mengambil data cuaca.',
                'disclaimer' => self::DISCLAIMER,
            ];
        }

        $cacheKey = sprintf('cargotrace_weather_%.4f_%.4f', $latitude, $longitude);
        $cached = Cache::get($cacheKey);
        if (is_array($cached) && ($cached['available'] ?? false) === true) {
            return array_merge($cached, ['location' => $locationLabel ?: ($cached['location'] ?? '')]);
        }

        $provider = config('services.weather.provider', 'open-meteo');
        $timeout = (int) config('services.weather.timeout', 5);

        try {
            if ($provider === 'openweathermap' && config('services.weather.key')) {
                $result = $this->fetchFromOpenWeatherMap($latitude, $longitude, $locationLabel, $timeout);
            } else {
                $result = $this->fetchFromOpenMeteo($latitude, $longitude, $locationLabel, $timeout);
            }

            if ($result['available'] ?? false) {
                Cache::put($cacheKey, $result, now()->addMinutes(15));
            }

            return $result;
        } catch (ConnectionException $e) {
            Log::warning('Weather API connection timeout/failure', [
                'lat' => $latitude,
                'lng' => $longitude,
                'error' => $e->getMessage(),
            ]);

            return [
                'available' => false,
                'status' => 'TIMEOUT_OR_NETWORK_ERROR',
                'location' => $locationLabel,
                'message' => 'Layanan cuaca eksternal sedang tidak merespons (timeout).',
                'disclaimer' => self::DISCLAIMER,
            ];
        } catch (Throwable $e) {
            Log::error('Unexpected Weather API failure', [
                'lat' => $latitude,
                'lng' => $longitude,
                'error' => $e->getMessage(),
            ]);

            return [
                'available' => false,
                'status' => 'UNEXPECTED_ERROR',
                'location' => $locationLabel,
                'message' => 'Gagal memproses data cuaca eksternal.',
                'disclaimer' => self::DISCLAIMER,
            ];
        }
    }

    protected function fetchFromOpenMeteo(float $lat, float $lng, string $locationLabel, int $timeout): array
    {
        $url = config('services.weather.base_url', 'https://api.open-meteo.com/v1/forecast');

        $response = Http::timeout($timeout)
            ->retry(2, 150, throw: false)
            ->acceptJson()
            ->get($url, [
                'latitude' => $lat,
                'longitude' => $lng,
                'current_weather' => 'true',
                'timezone' => 'UTC',
            ]);

        if (! $response->successful()) {
            Log::warning('Open-Meteo returned non-2xx HTTP status', [
                'status' => $response->status(),
                'lat' => $lat,
                'lng' => $lng,
            ]);

            return [
                'available' => false,
                'status' => 'API_ERROR',
                'http_status' => $response->status(),
                'location' => $locationLabel,
                'message' => "Layanan cuaca mengembalikan HTTP {$response->status()}.",
                'disclaimer' => self::DISCLAIMER,
            ];
        }

        $json = $response->json();
        $current = is_array($json) ? ($json['current_weather'] ?? null) : null;

        if (! is_array($current) || ! isset($current['temperature'])) {
            return [
                'available' => false,
                'status' => 'EMPTY_DATA',
                'location' => $locationLabel,
                'message' => 'Respons cuaca eksternal kosong atau tidak lengkap.',
                'disclaimer' => self::DISCLAIMER,
            ];
        }

        $code = (int) ($current['weathercode'] ?? 0);

        return [
            'available' => true,
            'status' => 'OK',
            'provider' => 'Open-Meteo API',
            'location' => $locationLabel,
            'latitude' => $lat,
            'longitude' => $lng,
            'temperature_c' => (float) $current['temperature'],
            'wind_speed_kmh' => (float) ($current['windspeed'] ?? 0),
            'weather_code' => $code,
            'condition' => $this->mapWmoCode($code),
            'observed_at' => $current['time'] ?? now()->toISOString(),
            'disclaimer' => self::DISCLAIMER,
        ];
    }

    protected function fetchFromOpenWeatherMap(float $lat, float $lng, string $locationLabel, int $timeout): array
    {
        $url = config('services.weather.openweather_url', 'https://api.openweathermap.org/data/2.5/weather');

        $response = Http::timeout($timeout)
            ->retry(2, 150, throw: false)
            ->acceptJson()
            ->get($url, [
                'lat' => $lat,
                'lon' => $lng,
                'appid' => config('services.weather.key'),
                'units' => 'metric',
            ]);

        if (! $response->successful()) {
            return [
                'available' => false,
                'status' => 'API_ERROR',
                'http_status' => $response->status(),
                'location' => $locationLabel,
                'message' => "OpenWeatherMap mengembalikan HTTP {$response->status()}.",
                'disclaimer' => self::DISCLAIMER,
            ];
        }

        $json = $response->json();
        if (! is_array($json) || ! isset($json['main']['temp'])) {
            return [
                'available' => false,
                'status' => 'EMPTY_DATA',
                'location' => $locationLabel,
                'message' => 'Data cuaca OpenWeatherMap kosong.',
                'disclaimer' => self::DISCLAIMER,
            ];
        }

        return [
            'available' => true,
            'status' => 'OK',
            'provider' => 'OpenWeatherMap API',
            'location' => $locationLabel,
            'latitude' => $lat,
            'longitude' => $lng,
            'temperature_c' => (float) $json['main']['temp'],
            'wind_speed_kmh' => round(((float) ($json['wind']['speed'] ?? 0)) * 3.6, 1),
            'weather_code' => (int) ($json['weather'][0]['id'] ?? 800),
            'condition' => ucfirst($json['weather'][0]['description'] ?? 'Cerah'),
            'observed_at' => now()->toISOString(),
            'disclaimer' => self::DISCLAIMER,
        ];
    }

    protected function mapWmoCode(int $code): string
    {
        return match (true) {
            $code === 0 => 'Cerah',
            in_array($code, [1, 2, 3], true) => 'Berawan Sebagian / Mendung',
            in_array($code, [45, 48], true) => 'Berkabut',
            in_array($code, [51, 53, 55, 61, 63, 65], true) => 'Hujan Ringan / Sedang',
            in_array($code, [80, 81, 82], true) => 'Hujan Deras',
            in_array($code, [95, 96, 99], true) => 'Badai Petir / Angin Kencang',
            default => 'Kondisi Maritim Normal',
        };
    }
}

