<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Logging\LoggingService;
use RuntimeException;

class HotelEmissionService
{
    public function calculate(array $payload)
    {
        $params = $payload['parameters'];

        $item = [
            'type' => 'hotel',
            'methodology' => strtoupper($params['methodology'] ?? 'SQUAKE'),
            'external_reference' => $params['external_reference'] ?? 'hotel_' . uniqid(),
        ];

        $fields = [
            'country', 'city', 'number_of_nights', 'stars', 'room_type',
            'hotel_type', 'hcmi_member', 'code', 'code_type'
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

        LoggingService::request('hotel', $squakePayload);

        $cacheKey = 'emission_hotel_' . md5(json_encode($squakePayload));

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($squakePayload) {
            $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->withToken(config('services.squake.token'))
                ->retry(3, 100)
                ->timeout(10)
                ->post(config('services.squake.url'), $squakePayload);

            LoggingService::response('hotel', $response->json());

            if ($response->failed()) {
                throw new RuntimeException('Squake hotel API failed: ' . $response->body(), $response->status());
            }

            return [
                "success" => true,
                "data" => $response->json()
            ];
        });
    }
}
