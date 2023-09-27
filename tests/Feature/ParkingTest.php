<?php

namespace Tests\Feature;

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

    public function testGetParkingsAuthenticate(): void
    {
        $response = $this->actingAs($this->user)->get('/api/parking');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [],
            ]);
    }

    public function testGetParkingsNoAuthenticate(): void
    {
        $response = $this->get('/api/parking');

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated',
            ]);
    }
}
