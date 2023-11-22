<?php

namespace Tests\Feature\Vacancies;

use App\Models\Parking;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VacanciesAuthenticateTest extends TestCase
{
    use RefreshDatabase;

    public const API_LOGIN_PATH = '/api/login';
    public const API_PARKING_PATH = '/api/vacancies';
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

    public function testGetVacancies(): void
    {
        $response = $this->get(self::API_PARKING_PATH."/{$this->parking->id}/", $this->AuthHeaders());

        $response->assertStatus(self::STATUS_CODE_CORRECT)
            ->assertJsonStructure([
                'data' => [],
            ]);
    }

    /**
     * Data Provider para testar diferentes cenários de criação de vagas.
     */
    public static function createOrUpdateVacancyDataProvider()
    {
        return [
            'vagas-com-corpo-correto' => [
                [
                    'number_of_vacancies' => 10,
                    'parking_id'          => 1,
                ],
                self::STATUS_CODE_CORRECT,
            ],
        ];
    }

    /**
     * @dataProvider createOrUpdateVacancyDataProvider
     */
    public function testCreateVacancies(array $requestData, int $expectedStatusCode): void
    {
        $response = $this->post(self::API_PARKING_PATH, $requestData, $this->AuthHeaders());

        $response->assertStatus($expectedStatusCode);

        $this->isFalse($response['data'][0]['errors']);

        $vacancy = new Vacancy();

        $countVacancies = $vacancy::where('parking_id', $requestData['parking_id'])->count();

        $this->assertGreaterThanOrEqual($requestData['number_of_vacancies'], $countVacancies);
    }
}
