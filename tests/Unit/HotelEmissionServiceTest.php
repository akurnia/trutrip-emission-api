<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\HotelEmissionService;
use Illuminate\Support\Facades\Http;

class HotelEmissionServiceTest extends TestCase
{
    public function test_hotel_emission_service_success()
    {
        Http::fake([
            '*' => Http::response([
                'carbon_quantity' => 3456,
                'carbon_unit' => 'gram',
                'items' => []
            ], 200)
        ]);

        $service = new HotelEmissionService();

        $payload = [
            'type' => 'hotel',
            'parameters' => [
                'methodology' => 'HCMI',
                'country' => 'AU',
                'city' => 'Villa La Angostura',
                'hotel_type' => 'urban_location',
                'stars' => 4,
                'hcmi_member' => false,
                'number_of_nights' => 1
            ]
        ];

        $result = $service->calculate($payload);

        $this->assertTrue($result['success']);
        $this->assertEquals(3456, $result['data']['carbon_quantity']);
    }
}
