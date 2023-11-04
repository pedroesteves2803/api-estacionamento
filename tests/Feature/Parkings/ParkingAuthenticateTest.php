<?php

namespace Tests\Feature\Parkings;

use App\Models\Parking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParkingAuthenticateTest extends TestCase
{
    use RefreshDatabase;

    public const API_LOGIN_PATH = '/api/login';
    public const API_PARKING_PATH = '/api/parking';
    public const ERROR_MESSAGE = 'Registro não encontrado';
    public const PASSWORD = 'password';
    public const STATUS_CODE_CORRECT = 200;
    public const STATUS_CODE_ERROR = 401;

    protected $user;
    protected $parking;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        // arrange
        $this->user = User::factory()->create();
        $this->parking = Parking::factory()->create();

        $this->token = $this->user->createToken($this->user->device_name)->plainTextToken;
    }

    private function AuthHeaders(): array
    {
        return [
            'Authorization' => 'Bearer '.$this->token,
            'Accept'        => 'application/json',
        ];
    }

    private function UnauthenticatedHeader(): array
    {
        return [
            'Accept' => 'application/json',
        ];
    }

    public function testLogin()
    {
        $body = [
            'email'    => $this->user->email,
            'password' => self::PASSWORD,
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post(self::API_LOGIN_PATH, $body);

        $this->token = $response->json()['data']['content']['token'];

        $response->assertStatus(self::STATUS_CODE_CORRECT)
            ->assertJsonStructure([
                'data' => [],
            ]);
    }

    public function testGetParkings(): void
    {
        $response = $this->get(self::API_PARKING_PATH, $this->AuthHeaders());

        $response->assertStatus(self::STATUS_CODE_CORRECT)
            ->assertJsonStructure([
                'data' => [],
            ]);
    }

    /**
     * Data Provider para testar diferentes cenários de criação de estacionamento.
     */
    public static function createOrUpdateParkingDataProvider()
    {
        return [
            'estacionamento-com-corpo-correto' => [
                [
                    'name'              => 'Estacionamento de sucesso 1',
                    'numberOfVacancies' => 100,
                    'active'            => 1,
                ],
                self::STATUS_CODE_CORRECT,
            ],
        ];
    }

    /**
     * @dataProvider createOrUpdateParkingDataProvider
     */
    public function testCreateParking(array $requestData, int $expectedStatusCode): void
    {
        $response = $this->post(self::API_PARKING_PATH, $requestData, $this->AuthHeaders());

        $response->assertStatus($expectedStatusCode);

        $this->isFalse($response['data']['errors']);

        if (self::STATUS_CODE_CORRECT === $expectedStatusCode and false === $response['data']['errors']) {
            $this->assertDatabaseHas('parkings', $requestData);
        } else {
            $this->assertEquals(self::ERROR_MESSAGE, $response['data']['message']);
        }
    }

    /**
     * @dataProvider createOrUpdateParkingDataProvider
     */
    public function testUpdateParking(array $requestData, int $expectedStatusCode): void
    {
        $response = $this->patch(self::API_PARKING_PATH."/{$this->parking->id}", $requestData, $this->AuthHeaders());

        $response->assertStatus(self::STATUS_CODE_CORRECT);

        if (self::STATUS_CODE_CORRECT === $expectedStatusCode and false === $response['data']['errors']) {
            $this->assertDatabaseHas('parkings', $requestData);
        } else {
            $this->assertEquals(self::ERROR_MESSAGE, $response['data']['message']);
        }
    }

    public function testGetParkingById(): void
    {
        $response = $this->get(self::API_PARKING_PATH."/{$this->parking->id}", $this->AuthHeaders());

        $response->assertStatus(self::STATUS_CODE_CORRECT)
            ->assertJsonStructure([
                'data' => [],
            ]);

        $this->assertNotNull($response['data']);
        $this->assertEquals(false, $response['data']['errors']);
        $this->assertNull($response['data']['message']);
        $this->assertIsArray($response['data']['content']);

        $content = $response['data']['content'];

        $this->assertEquals($this->parking->name, $content['nome']);
        $this->assertEquals($this->parking->numberOfVacancies, $content['numero_de_vagas']);
        $this->assertEquals($this->parking->active, $content['ativo']);
    }

    public function testDeleteById(): void
    {
        $response = $this->delete(self::API_PARKING_PATH."/{$this->parking->id}", [], $this->AuthHeaders());

        $response->assertStatus(204);
    }
}
