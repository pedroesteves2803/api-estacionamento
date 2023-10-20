<?php

namespace Tests\Feature\Cars;

use App\Models\Car;
use App\Models\Parking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarUnauthenticatedTest extends TestCase
{
    use RefreshDatabase;

    public const API_CAR_PATH = '/api/car';
    public const UNAUTHENTICATED_MESSAGE = 'Unauthenticated.';
    public const STATUS_CODE_CORRECT = 200;
    public const STATUS_CODE_ERROR = 401;

    protected $user;
    protected $token;
    protected $parking;
    protected $car;

    protected function setUp(): void
    {
        parent::setUp();

        // arrange
        $this->user = User::factory()->create();
        $this->parking = Parking::factory()->create();
        $this->car = Car::factory()->create();
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
        $this->assertEquals($response['message'], self::UNAUTHENTICATED_MESSAGE);
    }

    // Não autenticados

    public function testGetCarsNotAuthenticate(): void
    {
        $response = $this->get(self::API_CAR_PATH, $this->UnauthenticatedHeader());

        $this->UnauthenticatedResponse($response);
    }

    public static function createCarDataProvider()
    {
        return [
            'carro-com-corpo-incorreto' => [
                [
                    'plate'      => 'NEJ1472',
                    'model'      => 'Uno',
                    'color'      => 'Verde',
                    'parking_id' => 1,
                ],
                self::STATUS_CODE_CORRECT,
            ],
            'carro-com-corpo-correto-placa-padrão' => [
                [
                    'plate'      => 'HEU0535',
                    'model'      => 'Uno',
                    'color'      => 'Verde',
                    'parking_id' => 1,
                ],
                self::STATUS_CODE_CORRECT,
            ],
            'carro-com-corpo-correto-placa-mercosul' => [
                [
                    'plate'      => 'FBR2A23',
                    'model'      => 'Uno',
                    'color'      => 'Verde',
                    'parking_id' => 1,
                ],
                self::STATUS_CODE_CORRECT,
            ],
            'carro-com-corpo-correto-sem-id-do-estacionamento' => [
                [
                    'plate' => 'JTU2074',
                    'model' => 'Uno',
                    'color' => 'Verde',
                ],
                self::STATUS_CODE_CORRECT,
            ],
        ];
    }

    /**
     * @dataProvider createCarDataProvider
     */
    public function testCreateCarNotAuthenticate(array $requestData): void
    {
        $response = $this->post(self::API_CAR_PATH, $requestData, $this->UnauthenticatedHeader());

        $this->UnauthenticatedResponse($response);
    }

    public static function updateCarDataProvider()
    {
        return [
            'carro-com-corpo-incorreto' => [
                [
                    'plate'      => 'NEJ1472',
                    'model'      => 'Uno',
                    'color'      => 'Verde',
                    'parking_id' => 1,
                ],
                self::STATUS_CODE_CORRECT,
            ],
            'carro-com-corpo-correto-placa-padrão' => [
                [
                    'plate'      => 'HEU0535',
                    'model'      => 'Uno',
                    'color'      => 'Verde',
                    'parking_id' => 1,
                ],
                self::STATUS_CODE_CORRECT,
            ],
            'carro-com-corpo-correto-placa-mercosul' => [
                [
                    'plate'      => 'FBR2A23',
                    'model'      => 'Uno',
                    'color'      => 'Verde',
                    'parking_id' => 1,
                ],
                self::STATUS_CODE_CORRECT,
            ],
            'carro-com-corpo-correto-sem-id-do-estacionamento' => [
                [
                    'plate' => 'JTU2074',
                    'model' => 'Uno',
                    'color' => 'Verde',
                ],
                self::STATUS_CODE_CORRECT,
            ],
        ];
    }

    /**
     * @dataProvider updateCarDataProvider
     */
    public function testUpdateCarNotAuthenticate(array $requestData, int $expectedStatusCode): void
    {
        $response = $this->patch(self::API_CAR_PATH."/{$this->parking->id}/{$this->car->id}", $requestData, $this->UnauthenticatedHeader());

        $this->UnauthenticatedResponse($response);
    }

    public function testGetParkingByIdNotAuthenticate(): void
    {
        $response = $this->get(self::API_CAR_PATH."/{$this->parking->id}/{$this->car->id}", $this->UnauthenticatedHeader());

        $this->UnauthenticatedResponse($response);
    }

    public function testDeleteByIdNotAuthenticate()
    {
        $response = $this->delete(self::API_CAR_PATH."/{$this->parking->id}/{$this->car->id}", [], $this->UnauthenticatedHeader());

        $this->UnauthenticatedResponse($response);
    }

    public function testRegistersCarExitNotAuthenticate()
    {
        $response = $this->patch(self::API_CAR_PATH."/output/{$this->parking->id}/{$this->car->id}", [], $this->UnauthenticatedHeader());

        $this->UnauthenticatedResponse($response);
    }
}
