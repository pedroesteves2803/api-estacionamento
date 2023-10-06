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
        $response = $this->get(self::API_PARKING_PATH, $this->AuthHeaders());

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [],
            ]);
    }
    /**
     * Data Provider para testar diferentes cenários de criação de estacionamento.
     */
    public static function createParkingDataProvider()
    {
        return [
            // Caso de sucesso
            [
                [
                    'name' => 'Estacionamento de sucesso 0',
                    'numberOfVacancies' => 50,
                    'active' => 1,
                ],
                200,
            ],
            [
                [
                    'name' => 'Estacionamento de sucesso 1',
                    'numberOfVacancies' => 100,
                    'active' => 1,
                ],
                200,
            ],
            [
                [
                    'name' => 'Estacionamento de sucesso 2',
                    'numberOfVacancies' => 150,
                    'active' => 0,
                ],
                200,
            ],
        ];
    }

    /**
     * @dataProvider createParkingDataProvider
     */
    public function testCreateParking($requestData, $expectedStatusCode): void
    {
        $response = $this->post(self::API_PARKING_PATH, $requestData, $this->AuthHeaders());

        $response->assertStatus($expectedStatusCode);

        if ($expectedStatusCode === 200) {
            $this->assertDatabaseHas('parkings', $requestData);
        }
    }

    public function testUpdateParking(): void
    {
        $parking = Parking::factory()->create();

        $body = [
            'name'              => 'Estacionamento alterado',
            'numberOfVacancies' => 12,
            'active'            => 1,
        ];

        $response = $this->patch("/api/parking/{$parking->id}", $body, $this->AuthHeaders());

        $response->assertStatus(200);

        $updatedParking = Parking::find($parking->id);

        $this->assertEquals($body['name'], $updatedParking->name);
        $this->assertEquals($body['numberOfVacancies'], $updatedParking->numberOfVacancies);
        $this->assertEquals($body['active'], $updatedParking->active);
    }

    public function testGetParkingById(): void
    {
        $parking = Parking::factory()->create();

        $response = $this->get("/api/parking/{$parking->id}", $this->AuthHeaders());

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

        $response = $this->delete("/api/parking/{$parking->id}", [],$this->AuthHeaders());

        $response->assertStatus(204);
    }

    public function testGetParkingsNotAuthenticate(): void
    {
        $response = $this->get(self::API_PARKING_PATH, $this->UnauthenticatedHeader());

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

        $response = $this->post(self::API_PARKING_PATH, $body, $this->UnauthenticatedHeader());

        $response->assertStatus(401);
        $this->assertEquals($response['message'], self::UNAUTHENTICATED_MESSAGE);
    }

    public function testUpdateParkingNotAuthenticate(): void
    {
        $parking = Parking::factory()->create();

        $body = [
            'name'              => 'Estacionamento alterado',
            'numberOfVacancies' => 12,
            'active'            => 1,
        ];

        $response = $this->patch("/api/parking/{$parking->id}", $body, $this->UnauthenticatedHeader());

        $response->assertStatus(401);
        $this->assertEquals($response['message'], self::UNAUTHENTICATED_MESSAGE);
    }

    public function testGetParkingByIdNotAuthenticate(): void
    {
        $parking = Parking::factory()->create();

        $response = $this->get("/api/parking/{$parking->id}", $this->UnauthenticatedHeader());

        $response->assertStatus(401);
        $this->assertEquals($response['message'], self::UNAUTHENTICATED_MESSAGE);
    }

    public function testDeleteByIdNotAuthenticate(): void
    {
        $parking = Parking::factory()->create();

        $response = $this->delete("/api/parking/{$parking->id}", [], $this->UnauthenticatedHeader());

        $response->assertStatus(401);
        $this->assertEquals($response['message'], self::UNAUTHENTICATED_MESSAGE);
    }


}
