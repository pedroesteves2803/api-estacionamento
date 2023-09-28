<?php

namespace Tests\Feature;

use App\Models\Parking;
use App\Models\User;
use Tests\TestCase;

class ParkingTest extends TestCase
{
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function testGetParkings(): void
    {
        $response = $this->actingAs($this->user)->get('/api/parking');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [],
            ]);
    }

    public function testCreateParking(): void
    {
        $body = [
            'name'              => 'Estacionamento',
            'numberOfVacancies' => 100,
            'active'            => 1,
        ];

        $response = $this->actingAs($this->user)
            ->post('/api/parking', $body);

        $response->assertStatus(200);

        $this->assertDatabaseHas('parkings', $body);
    }

    public function testUpdateParking(): void
    {
        $parking = Parking::factory()->create();

        $body = [
            'name'              => 'Estacionamento alteraro',
            'numberOfVacancies' => 12,
            'active'            => 1,
        ];

        $response = $this->actingAs($this->user)
            ->post("/api/parking/{$parking->id}", $body);

        $response->assertStatus(200);

        $updatedParking = Parking::find($parking->id);

        $this->assertEquals($body['name'], $updatedParking->name);
        $this->assertEquals($body['numberOfVacancies'], $updatedParking->numberOfVacancies);
        $this->assertEquals($body['active'], $updatedParking->active);
    }
}
