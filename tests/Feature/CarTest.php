<?php

namespace Tests\Feature;

use App\Models\Car;
use App\Models\Parking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarTest extends TestCase
{
    use RefreshDatabase;

    const API_LOGIN_PATH = '/api/login';
    const API_CAR_PATH = '/api/car';
    const UNAUTHENTICATED_MESSAGE = 'Unauthenticated.';
    const ERROR_MESSAGE = 'Registro não encontrado';
    const PASSWORD = 'password';
    const STATUS_CODE_CORRECT = 200;
    const STATUS_CODE_ERROR = 401;

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
        $response = $this->get(self::API_CAR_PATH, $this->AuthHeaders());

        $response->assertStatus(self::STATUS_CODE_CORRECT)
            ->assertJsonStructure([
                'data' => [],
            ]);
    }

    public static function createCarDataProvider()
    {
        return [
            'carro-com-corpo-incorreto' => [
                [
                    "plate" =>  "NEJ1472",
                    "model" =>  "Uno",
                    "color" =>  "Verde",
                    "parking_id" =>  1
                ],
                self::STATUS_CODE_CORRECT,
            ],
            'carro-com-corpo-correto-placa-padrão' => [
                [
                    "plate" =>  "HEU0535",
                    "model" =>  "Uno",
                    "color" =>  "Verde",
                    "parking_id" =>  1
                ],
                self::STATUS_CODE_CORRECT,
            ],
            'carro-com-corpo-correto-placa-mercosul' => [
                [
                    "plate" =>  "FBR2A23",
                    "model" =>  "Uno",
                    "color" =>  "Verde",
                    "parking_id" =>  1
                ],
                self::STATUS_CODE_CORRECT,
            ],
            'carro-com-corpo-correto-sem-id-do-estacionamento' => [
                [
                    "plate" =>  "JTU2074",
                    "model" =>  "Uno",
                    "color" =>  "Verde",
                ],
                self::STATUS_CODE_CORRECT,
            ]
        ];
    }

    /**
     * @dataProvider createCarDataProvider
     */
    public function testCreateCar(array $requestData, int $expectedStatusCode): void
    {
        $response = $this->post(self::API_CAR_PATH, $requestData, $this->AuthHeaders());

        $response->assertStatus($expectedStatusCode);

        $this->isFalse($response['data']['errors']);

        if ($expectedStatusCode === self::STATUS_CODE_CORRECT and $response['data']['errors'] === false) {
            $this->assertDatabaseHas('cars', $requestData);
        }else{
            $this->assertEquals($response['data']['message'], self::ERROR_MESSAGE);
        }
    }


    public static function updateCarDataProvider()
    {
        return [
            'carro-com-corpo-incorreto' => [
                [
                    "plate" =>  "NEJ1472",
                    "model" =>  "Uno",
                    "color" =>  "Verde",
                    "parking_id" =>  1
                ],
                self::STATUS_CODE_CORRECT,
            ],
            'carro-com-corpo-correto-placa-padrão' => [
                [
                    "plate" =>  "HEU0535",
                    "model" =>  "Uno",
                    "color" =>  "Verde",
                    "parking_id" =>  1
                ],
                self::STATUS_CODE_CORRECT,
            ],
            'carro-com-corpo-correto-placa-mercosul' => [
                [
                    "plate" =>  "FBR2A23",
                    "model" =>  "Uno",
                    "color" =>  "Verde",
                    "parking_id" =>  1
                ],
                self::STATUS_CODE_CORRECT,
            ],
            'carro-com-corpo-correto-sem-id-do-estacionamento' => [
                [
                    "plate" =>  "JTU2074",
                    "model" =>  "Uno",
                    "color" =>  "Verde",
                ],
                self::STATUS_CODE_CORRECT,
            ]
        ];
    }

    /**
     * @dataProvider updateCarDataProvider
     */
    public function testUpdateCar(array $requestData, int $expectedStatusCode): void
    {
        $response = $this->patch(self::API_CAR_PATH."/{$this->parking->id}/{$this->car->id}", $requestData, $this->AuthHeaders());

        $response->assertStatus(self::STATUS_CODE_CORRECT);

        if ($expectedStatusCode === self::STATUS_CODE_CORRECT and $response['data']['errors'] === false) {
            $this->assertDatabaseHas('cars', $requestData);
        }else{
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

        $this->assertNotNull($response['data']);
        $this->assertEquals($response['data']['errors'], false);
        $this->assertNull($response['data']['message']);
        $this->assertIsArray($response['data']['content']);

        $content = $response['data']['content'];

        $this->assertEquals($this->car->plate, $content['placa']);
        $this->assertEquals($this->car->model, $content['modelo']);
        $this->assertEquals($this->car->color, $content['cor']);
        $this->assertEquals($this->car->parking_id, $content['estacionamento_id']);

    }

}
