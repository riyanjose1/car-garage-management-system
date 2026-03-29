<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserBookingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_and_book_service(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'password' => bcrypt('password'),
        ]);

        $vehicle = Vehicle::create([
            'user_id' => $user->id,
            'brand' => 'Honda',
            'model' => 'City',
            'plate_number' => 'KL07AB1234',
            'year' => 2020,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/user/dashboard');

        $this->actingAs($user);

        $bookingResponse = $this->post('/appointments', [
            'vehicle_id' => $vehicle->id,
            'service_date' => now()->addDay()->format('Y-m-d'),
            'service_time' => '10:30',
            'service_type' => 'General Service',
            'notes' => 'Test booking',
        ]);

        $bookingResponse->assertRedirect('/user/dashboard');

        $this->assertDatabaseHas('appointments', [
            'user_id' => $user->id,
            'vehicle_id' => $vehicle->id,
            'service_type' => 'General Service',
            'status' => 'Pending',
        ]);
    }
}