<?php

namespace Tests\Feature;

use App\Models\Parking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParkingTest extends TestCase
{
    use RefreshDatabase;

    const API_LOGIN_PATH = '/api/login';
    const API_PARKING_PATH = '/api/parking';
    const UNAUTHENTICATED_MESSAGE = 'Unauthenticated.';

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->token = $this->user->createToken($this->user->device_name)->plainTextToken;
    }

    public function testLogin(){

        $body = [
            'email' => $this->user->email,
            'password' => 'password',
        ];

        $response = $this->withHeaders([
            'Accept'        => 'application/json',
        ])->post(self::API_LOGIN_PATH, $body);

        $this->token = $response->json()['data']['content']['token'];

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [],
            ]);
    }

    public function testGetParkings(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept'        => 'application/json',
        ])->get(self::API_PARKING_PATH);

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

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept'        => 'application/json',
        ])->post(self::API_PARKING_PATH, $body);

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

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept'        => 'application/json',
        ])->patch("/api/parking/{$parking->id}", $body);

        $response->assertStatus(200);

        $updatedParking = Parking::find($parking->id);

        $this->assertEquals($body['name'], $updatedParking->name);
        $this->assertEquals($body['numberOfVacancies'], $updatedParking->numberOfVacancies);
        $this->assertEquals($body['active'], $updatedParking->active);
    }

    public function testGetParkingById(): void
    {
        $parking = Parking::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept'        => 'application/json',
        ])->get("/api/parking/{$parking->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [],
            ]);

        $this->assertNotNull($response['data']);
        $this->assertEquals($response['data']['errors'], false);
        $this->assertNull($response['data']['message']);
        $this->assertIsArray($response['data']['content']);

        $content = $response['data']['content'];

        $this->assertEquals($parking->name, $content['nome']);
        $this->assertEquals($parking->numberOfVacancies, $content['numero_de_vagas']);
        $this->assertEquals($parking->active, $content['ativo']);
    }

    public function testDeleteById(): void
    {
        $parking = Parking::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept'        => 'application/json',
        ])->delete("/api/parking/{$parking->id}");

        $response->assertStatus(204);
    }

    public function testGetParkingsNotAuthenticate(): void
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->get(self::API_PARKING_PATH);

        $response->assertStatus(401);
        $this->assertEquals($response['message'], self::UNAUTHENTICATED_MESSAGE);
    }

    public function testCreateNotAuthenticate(): void
    {
        $body = [
            'name'              => 'Estacionamento',
            'numberOfVacancies' => 100,
            'active'            => 1,
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post(self::API_PARKING_PATH, $body);

        $response->assertStatus(401);
        $this->assertEquals($response['message'], self::UNAUTHENTICATED_MESSAGE);
    }

    public function testUpdateParkingNotAuthenticate(): void
    {
        $parking = Parking::factory()->create();

        $body = [
            'name'              => 'Estacionamento alteraro',
            'numberOfVacancies' => 12,
            'active'            => 1,
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->patch("/api/parking/{$parking->id}", $body);

        $response->assertStatus(401);
        $this->assertEquals($response['message'], self::UNAUTHENTICATED_MESSAGE);
    }

    public function testGetParkingByIdNotAuthenticate(): void
    {
        $parking = Parking::factory()->create();

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->get("/api/parking/{$parking->id}");

        $response->assertStatus(401);
        $this->assertEquals($response['message'], self::UNAUTHENTICATED_MESSAGE);
    }

    public function testDeleteByIdNotAuthenticate(): void
    {
        $parking = Parking::factory()->create();

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->delete("/api/parking/{$parking->id}");

        $response->assertStatus(401);
        $this->assertEquals($response['message'], self::UNAUTHENTICATED_MESSAGE);
    }


}
