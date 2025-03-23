<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;

class EmissionControllerTest extends TestCase
{
    public function test_train_emission_controller_success()
    {
        Http::fake([
            '*' => Http::response([
                'carbon_quantity' => 19045,
                'carbon_unit' => 'gram',
                'items' => []
            ], 200)
        ]);

        $response = $this->postJson('/api/emissions/calculate', [
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
        ], [
            'X-API-TOKEN' => 'supersecuretoken123'
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'carbon_quantity' => 19045
                     ]
                 ]);
    }

    public function test_hotel_emission_controller_success()
    {
        Http::fake([
            '*' => Http::response([
                'carbon_quantity' => 29630,
                'carbon_unit' => 'gram',
                'items' => []
            ], 200)
        ]);

        $response = $this->postJson('/api/emissions/calculate', [
            'type' => 'hotel',
            'parameters' => [
                'methodology' => 'HCMI',
                'external_reference' => 'hcmi_001',
                'country' => 'AU',
                'city' => 'Villa La Angostura',
                'hotel_type' => 'urban_location',
                'stars' => 4,
                'hcmi_member' => false,
                'number_of_nights' => 1
            ]
        ], [
            'X-API-TOKEN' => 'supersecuretoken123'
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'carbon_quantity' => 29630
                     ]
                 ]);
    }

    public function test_calculate_flight_emission_success()
    {
        Http::fake([
            '*' => Http::response([
                'carbon_quantity' => 298996,
                'carbon_unit' => 'gram',
                'items' => null
            ], 200)
        ]);

        $response = $this->postJson('/api/emissions/calculate', [
            'type' => 'flight',
            'parameters' => [
                'methodology' => 'TIM',
                'origin' => 'BER',
                'destination' => 'MAD',
                'booking_class' => 'business',
                'aircraft_type' => '320'
            ]
        ], [
            'X-API-TOKEN' => 'supersecuretoken123'
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'carbon_quantity' => 298996
                     ]
                 ]);
    }

    public function test_validation_error_response()
    {
        $response = $this->postJson('/api/emissions/calculate', [], [
            'X-API-TOKEN' => 'supersecuretoken123'
        ]);

        $response->assertStatus(422);
    }

    public function test_unauthorized_access()
    {
        $response = $this->postJson('/api/emissions/calculate', [
            'type' => 'flight',
            'parameters' => [
                'origin' => 'BER',
                'destination' => 'MAD',
                'methodology' => 'TIM'
            ]
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'message' => 'Unauthorized'
                 ]);
    }

    public function test_missing_type_parameter()
    {
        $response = $this->postJson('/api/emissions/calculate', [
            'parameters' => [
                'origin' => 'BER',
                'destination' => 'MAD',
                'methodology' => 'TIM'
            ]
        ], [
            'X-API-TOKEN' => 'supersecuretoken123'
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['type']);
    }

    public function test_missing_parameters_array()
    {
        $response = $this->postJson('/api/emissions/calculate', [
            'type' => 'flight'
        ], [
            'X-API-TOKEN' => 'supersecuretoken123'
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['parameters']);
    }

    public function test_invalid_type_value()
    {
        $response = $this->postJson('/api/emissions/calculate', [
            'type' => 'rocket',
            'parameters' => [
                'origin' => 'BER',
                'destination' => 'MAD',
                'methodology' => 'TIM'
            ]
        ], [
            'X-API-TOKEN' => 'supersecuretoken123'
        ]);

        $response->assertStatus(422)
          ->assertJsonValidationErrors(['type']);
    }
}
