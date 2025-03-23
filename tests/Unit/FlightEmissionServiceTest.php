<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\FlightEmissionService;
use Illuminate\Support\Facades\Http;

class FlightEmissionServiceTest extends TestCase
{
    public function test_flight_emission_service_success()
    {
        Http::fake([
            '*' => Http::response([
                'carbon_quantity' => 12345,
                'carbon_unit' => 'gram',
                'items' => []
            ], 200)
        ]);

        $service = new FlightEmissionService();

        $payload = [
            'type' => 'flight',
            'parameters' => [
                'methodology' => 'TIM',
                'origin' => 'BER',
                'destination' => 'MAD',
                'booking_class' => 'business',
                'aircraft_type' => '320'
            ]
        ];

        $result = $service->calculate($payload);

        $this->assertTrue($result['success']);
        $this->assertEquals(12345, $result['data']['carbon_quantity']);
    }
}
