<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Logging\LoggingService;
use RuntimeException;

class TrainEmissionService
{
    public function calculate(array $payload)
    {
        $params = $payload['parameters'];

        $item = [
            'type' => 'train',
            'methodology' => strtoupper($params['methodology'] ?? 'SQUAKE'),
            'external_reference' => $params['external_reference'] ?? 'train_' . uniqid(),
        ];

        $fields = [
            'origin', 'destination', 'number_of_travelers', 'train_type', 'seat_type',
            'fuel_type', 'operator_name', 'country', 'state', 'year', 'energy_scope',
            'distance_in_km'
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

        LoggingService::request('train', $squakePayload);

        $cacheKey = 'emission_train_' . md5(json_encode($squakePayload));

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($squakePayload) {
            $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->withToken(config('services.squake.token'))
                ->retry(3, 100)
                ->timeout(10)
                ->post(config('services.squake.url'), $squakePayload);

            LoggingService::response('train', $response->json());

            if ($response->failed()) {
                throw new RuntimeException('Squake train API failed: ' . $response->body(), $response->status());
            }

            return [
                "success" => true,
                "data" => $response->json()
            ];
        });
    }
}
