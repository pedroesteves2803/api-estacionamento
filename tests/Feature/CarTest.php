<?php

namespace Tests\Feature;

use App\Models\Parking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarTest extends TestCase
{
    use RefreshDatabase;

    const API_LOGIN_PATH = '/api/login';
    const API_PARKING_PATH = '/api/car';
    const UNAUTHENTICATED_MESSAGE = 'Unauthenticated.';
    const ERROR_MESSAGE = 'Registro não encontrado';
    const PASSWORD = 'password';
    const STATUS_CODE_CORRECT = 200;
    const STATUS_CODE_ERROR = 401;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        // arrange
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken($this->user->device_name)->plainTextToken;
    }

    private function AuthHeaders() : array
    {
        return [
            'Authorization' => 'Bearer '.$this->token,
            'Accept'        => 'application/json',
        ];
    }

    private function UnauthenticatedHeader() : array
    {
        return [
            'Accept'        => 'application/json',
        ];
    }

    public function testLogin(){

        $body = [
            'email' => $this->user->email,
            'password' => self::PASSWORD,
        ];

        $response = $this->withHeaders([
            'Accept'        => 'application/json',
        ])->post(self::API_LOGIN_PATH, $body);

        $this->token = $response->json()['data']['content']['token'];

        $response->assertStatus(self::STATUS_CODE_CORRECT)
            ->assertJsonStructure([
                'data' => [],
            ]);
    }

    public function testGetCars(): void
    {
        $response = $this->get(self::API_PARKING_PATH, $this->AuthHeaders());

        $response->assertStatus(self::STATUS_CODE_CORRECT)
            ->assertJsonStructure([
                'data' => [],
            ]);
    }


}
