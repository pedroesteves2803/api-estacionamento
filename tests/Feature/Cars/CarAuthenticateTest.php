<?php

namespace Tests\Feature\Cars;

use App\Models\Car;
use App\Models\Parking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarAuthenticateTest extends TestCase
{
    use RefreshDatabase;

    public const API_CAR_PATH = '/api/car';
    public const ERROR_MESSAGE = 'Registro não encontrado';
    public const PASSWORD = 'password';
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
        $this->token = $this->user->createToken($this->user->device_name)->plainTextToken;
    }

    private function AuthHeaders(): array
    {
        return [
            'Authorization' => 'Bearer '.$this->token,
            'Accept'        => 'application/json',
        ];
    }

    private function checkResponseBody($response) :void
    {
        $this->assertNotNull($response['data']);
        $this->assertEquals($response['data']['errors'], false);
        $this->assertNull($response['data']['message']);
        $this->assertIsArray($response['data']['content']);
    }

    public function testGetCars(): void
    {
        $response = $this->get(self::API_CAR_PATH, $this->AuthHeaders());

        $response->assertStatus(self::STATUS_CODE_CORRECT)
            ->assertJsonStructure([
                'data' => [],
            ]);
    }

    public static function createOrUpdateCarDataProvider()
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
     * @dataProvider createOrUpdateCarDataProvider
     */
    public function testCreateCar(array $requestData, int $expectedStatusCode): void
    {
        $response = $this->post(self::API_CAR_PATH, $requestData, $this->AuthHeaders());

        $response->assertStatus($expectedStatusCode);

        $this->isFalse($response['data']['errors']);

        if (self::STATUS_CODE_CORRECT === $expectedStatusCode and false === $response['data']['errors']) {
            $this->assertDatabaseHas('cars', $requestData);
        } else {
            $this->assertEquals($response['data']['message'], self::ERROR_MESSAGE);
        }
    }

    /**
     * @dataProvider createOrUpdateCarDataProvider
     */
    public function testUpdateCar(array $requestData, int $expectedStatusCode): void
    {
        $response = $this->patch(self::API_CAR_PATH."/{$this->parking->id}/{$this->car->id}", $requestData, $this->AuthHeaders());

        $response->assertStatus(self::STATUS_CODE_CORRECT);

        if (self::STATUS_CODE_CORRECT === $expectedStatusCode and false === $response['data']['errors']) {
            $this->assertDatabaseHas('cars', $requestData);
        } else {
            $this->assertEquals($response['data']['message'], self::ERROR_MESSAGE);
        }
    }

    public function testGetParkingById(): void
    {
        $response = $this->get(self::API_CAR_PATH."/{$this->parking->id}/{$this->car->id}", $this->AuthHeaders());

        $response->assertStatus(self::STATUS_CODE_CORRECT)
            ->assertJsonStructure([
                'data' => [],
            ]);

        $this->checkResponseBody($response);

        $content = $response['data']['content'];

        $this->assertEquals($this->car->plate, $content['placa']);
        $this->assertEquals($this->car->model, $content['modelo']);
        $this->assertEquals($this->car->color, $content['cor']);
        $this->assertEquals($this->car->parking_id, $content['estacionamento_id']);
    }

    public function testDeleteById()
    {
        $response = $this->delete(self::API_CAR_PATH."/{$this->parking->id}/{$this->car->id}", [], $this->AuthHeaders());

        $response->assertStatus(204);
    }

    public function testRegistersCarExit()
    {
        $response = $this->patch(self::API_CAR_PATH."/output/{$this->parking->id}/{$this->car->id}", [], $this->AuthHeaders());

        $content = $response['data']['content'];

        $count = count($content);

        $this->assertNotNull($content['saida']);
        $this->assertNotNull($content['valor_para_pagamento']);
        $this->assertEquals($count, 8);
    }
}
