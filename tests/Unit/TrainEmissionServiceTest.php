<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\TrainEmissionService;
use Illuminate\Support\Facades\Http;

class TrainEmissionServiceTest extends TestCase
{
    public function test_train_emission_service_success()
    {
        Http::fake([
            '*' => Http::response([
                'carbon_quantity' => 4567,
                'carbon_unit' => 'gram',
                'items' => []
            ], 200)
        ]);

        $service = new TrainEmissionService();

        $payload = [
            'type' => 'train',
            'parameters' => [
                'methodology' => 'SQUAKE',
                'origin' => 'Nice',
                'destination' => 'ORY',
                'number_of_travelers' => 2,
                'train_type' => 'high_speed',
                'seat_type' => 'second_class',
                'fuel_type' => 'diesel',
                'operator_name' => 'sncf'
            ]
        ];

        $result = $service->calculate($payload);

        $this->assertTrue($result['success']);
        $this->assertEquals(4567, $result['data']['carbon_quantity']);
    }
}
