<?php

namespace Tests\Feature\Parkings;

use App\Models\Parking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParkingUnauthenticatedTest extends TestCase
{
    use RefreshDatabase;

    public const API_PARKING_PATH = '/api/parking';
    public const UNAUTHENTICATED_MESSAGE = 'Unauthenticated.';
    public const ERROR_MESSAGE = 'Registro não encontrado';
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

    private function UnauthenticatedHeader(): array
    {
        return [
            'Accept' => 'application/json',
        ];
    }

    private function UnauthenticatedResponse($response)
    {
        $response->assertStatus(self::STATUS_CODE_ERROR);
        $this->assertEquals(self::UNAUTHENTICATED_MESSAGE, $response['message']);
    }

    /**
     * Data Provider para testar diferentes cenários de criação de estacionamento.
     */
    public static function createParkingDataProvider()
    {
        return [
            'estacionamento-com-corpo-incorreto' => [
                [
                    'name'              => 'Estacionamento de sucesso 0',
                    'numberOfVacancies' => 50,
                ],
                self::STATUS_CODE_ERROR,
            ],
            'estacionamento-com-corpo-correto' => [
                [
                    'name'              => 'Estacionamento de sucesso 1',
                    'numberOfVacancies' => 100,
                    'active'            => 1,
                ],
                self::STATUS_CODE_ERROR,
            ],
        ];
    }

    /**
     * Data Provider para testar diferentes cenários de criação de estacionamento.
     */
    public static function updateParkingDataProvider()
    {
        return [
            'estacionamento-com-corpo-incorreto' => [
                [
                    'name'              => 'Estacionamento de sucesso 0',
                    'numberOfVacancies' => 50,
                ],
                self::STATUS_CODE_ERROR,
            ],
            'estacionamento-com-corpo-correto' => [
                [
                    'name'              => 'Estacionamento de sucesso 1',
                    'numberOfVacancies' => 100,
                    'active'            => 1,
                ],
                self::STATUS_CODE_ERROR,
            ],
        ];
    }

    public function testGetParkingsNotAuthenticate(): void
    {
        $response = $this->get(self::API_PARKING_PATH, $this->UnauthenticatedHeader());

        $this->UnauthenticatedResponse($response);
    }

    /**
     * @dataProvider createParkingDataProvider
     */
    public function testCreateNotAuthenticate(array $requestData, int $expectedStatusCode): void
    {
        $response = $this->post(self::API_PARKING_PATH, $requestData, $this->UnauthenticatedHeader());

        $response->assertStatus($expectedStatusCode);
        $this->assertEquals($response['message'], self::UNAUTHENTICATED_MESSAGE);
    }

    /**
     * @dataProvider updateParkingDataProvider
     */
    public function testUpdateParkingNotAuthenticate(array $requestData, int $expectedStatusCode): void
    {
        $response = $this->patch(self::API_PARKING_PATH."/{$this->parking->id}", $requestData, $this->UnauthenticatedHeader());

        $response->assertStatus($expectedStatusCode);
        $this->assertEquals($response['message'], self::UNAUTHENTICATED_MESSAGE);
    }

    public function testGetParkingByIdNotAuthenticate(): void
    {
        $response = $this->get(self::API_PARKING_PATH."/{$this->parking->id}", $this->UnauthenticatedHeader());

        $this->UnauthenticatedResponse($response);
    }

    public function testDeleteByIdNotAuthenticate(): void
    {
        $response = $this->delete(self::API_PARKING_PATH."/{$this->parking->id}", [], $this->UnauthenticatedHeader());

        $this->UnauthenticatedResponse($response);
    }
}
