<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Logging\LoggingService;
use RuntimeException;

class FlightEmissionService
{
    public function calculate(array $payload)
    {
        $params = $payload['parameters'];

        $item = [
            'type' => 'flight',
            'methodology' => strtolower($params['methodology'] ?? 'tim'),
            'external_reference' => $params['external_reference'] ?? 'flight_' . uniqid(),
        ];

        $fields = [
            'origin', 'destination', 'booking_class', 'aircraft_type', 'number_of_travelers',
            'fare_class', 'airline', 'flight_number', 'departure_date', 'radiative_forcing_index',
            'sustainable_fuels'
        ];

        foreach ($fields as $field) {
            if (isset($params[$field])) {
                $item[$field] = $params[$field];
            }
        }

        $squakePayload = [
            'expand' => ['items'],
            'items' => [$item]
        ];

        LoggingService::request('flight', $squakePayload);

        $cacheKey = 'emission_flight_' . md5(json_encode($squakePayload));

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($squakePayload) {
            $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->withToken(config('services.squake.token'))
                ->retry(3, 100)
                ->timeout(10)
                ->post(config('services.squake.url'), $squakePayload);

            LoggingService::response('flight', $response->json());

            if ($response->failed()) {
                throw new RuntimeException('Squake flight API failed: ' . $response->body(), $response->status());
            }

            return [
                "success" => true,
                "data" => $response->json()
            ];
        });
    }
}
