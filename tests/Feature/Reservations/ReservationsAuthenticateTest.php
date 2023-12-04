<?php

namespace Tests\Feature\Reservations;

use App\Models\Car;
use App\Models\Parking;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationsAuthenticateTest extends TestCase
{
    use RefreshDatabase;

    public const API_LOGIN_PATH = '/api/login';
    public const API_RESERVATION_PATH = '/api/reservation';
    public const ERROR_MESSAGE_PARKING = 'Estacionamento não existe!';
    public const ERROR_MESSAGE = 'Registro não encontrado';
    public const PASSWORD = 'password';
    public const STATUS_CODE_CORRECT = 200;
    public const STATUS_CODE_ERROR = 401;

    protected $user;
    protected $token;
    protected $parking;
    protected $vacancy;
    protected $car;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->parking = Parking::factory()->create();
        $this->vacancy = Vacancy::factory()->create();
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

    private function checkResponseBody($response): void
    {
        $this->assertNotNull($response['data']);
        $this->isFalse($response['data']['errors']);
        $this->assertNull($response['data']['message']);
        $this->assertIsArray($response['data']['content']);
    }

    public static function createOrUpdateReservationDataProvider()
    {
        return [
            'reserva-com-corpo-correto' => [
                [
                    'parking_id' => 1,
                    'vacancy_id' => 1,
                    'car_id'     => 1,
                    'start_date' => '2023-01-01',
                    'status'     => 1,
                ],
                self::STATUS_CODE_CORRECT,
            ],
        ];
    }

    /**
     * @dataProvider createOrUpdateReservationDataProvider
     */
    public function testCreateReservation(array $requestData, int $expectedStatusCode): void
    {
        $response = $this->post(self::API_RESERVATION_PATH, $requestData, $this->AuthHeaders());

        $response->assertStatus($expectedStatusCode);

        $this->checkResponseBody($response);

        $this->assertDatabaseHas('reservations', $requestData);
    }
}
