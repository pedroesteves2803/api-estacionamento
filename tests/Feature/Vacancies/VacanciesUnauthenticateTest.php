<?php

namespace Tests\Feature\Vacancies;

use App\Models\Parking;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VacanciesUnauthenticateTest extends TestCase
{
    use RefreshDatabase;

    public const API_LOGIN_PATH = '/api/login';
    public const API_VACANCIE_PATH = '/api/vacancies';
    public const UNAUTHENTICATED_MESSAGE = 'Unauthenticated.';
    public const ERROR_MESSAGE = 'Registro não encontrado';
    public const STATUS_CODE_CORRECT = 200;
    public const STATUS_CODE_ERROR = 401;

    protected $user;
    protected $parking;
    protected $vacancy;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        // arrange
        $this->user = User::factory()->create();
        $this->parking = Parking::factory()->create();
        $this->vacancy = Vacancy::factory()->create();

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

    public function testGetVacancies(): void
    {
        $response = $this->get(self::API_VACANCIE_PATH."/{$this->parking->id}/", $this->UnauthenticatedHeader());

        $this->UnauthenticatedResponse($response);
    }

    /**
     * Data Provider para testar diferentes cenários de criação de vagas.
     */
    public static function createVacancyDataProvider()
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
     * @dataProvider createVacancyDataProvider
     */
    public function testCreateVacancies(array $requestData, int $expectedStatusCode): void
    {
        $response = $this->post(self::API_VACANCIE_PATH, $requestData, $this->UnauthenticatedHeader());

        $this->UnauthenticatedResponse($response);
    }

    /**
     * Data Provider para testar diferentes cenários de criação de vagas.
     */
    public static function updateVacancyDataProvider()
    {
        return [
            'vagas-com-corpo-correto' => [
                [
                    'parking_id' => 1,
                    'number'     => 99,
                    'available'  => true,
                ],
                self::STATUS_CODE_CORRECT,
            ],
        ];
    }

    /**
     * @dataProvider updateVacancyDataProvider
     */
    public function testUpdateVacancies(array $requestData, int $expectedStatusCode): void
    {
        $response = $this->patch(self::API_VACANCIE_PATH."/{$this->parking->id}/{$this->vacancy->id}", $requestData, $this->UnauthenticatedHeader());

        $this->UnauthenticatedResponse($response);
    }

    public function testGetVacancyById()
    {
        $response = $this->get(self::API_VACANCIE_PATH."/{$this->parking->id}/{$this->vacancy->id}", $this->UnauthenticatedHeader());

        $this->UnauthenticatedResponse($response);
    }

    public function testDeleteById()
    {
        $response = $this->delete(self::API_VACANCIE_PATH."/{$this->parking->id}/{$this->vacancy->id}", [], $this->UnauthenticatedHeader());

        $this->UnauthenticatedResponse($response);
    }
}
