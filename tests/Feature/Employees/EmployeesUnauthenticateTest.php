<?php

namespace Tests\Feature\Parkings;

use App\Models\Employees;
use App\Models\Parking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeesUnauthenticateTest extends TestCase
{
    use RefreshDatabase;

    public const API_EMPLOYEES_PATH = '/api/employees';
    public const UNAUTHENTICATED_MESSAGE = 'Unauthenticated.';
    public const STATUS_CODE_CORRECT = 200;
    public const STATUS_CODE_ERROR = 401;

    protected $parking;
    protected $employees;

    protected function setUp(): void
    {
        parent::setUp();

        // arrange
        $this->parking = Parking::factory()->create();
        $this->employees = Employees::factory()->create();
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

    public function testGetEmployees(): void
    {
        $response = $this->get(self::API_EMPLOYEES_PATH, $this->UnauthenticatedHeader());

        $this->UnauthenticatedResponse($response);
    }

    /**
     * Data Provider para testar diferentes cenários de criação de estacionamento.
     */
    public static function createOrUpdateEmployeesDataProvider()
    {
        return [
            'estacionamento-com-corpo-incorreto' => [
                [
                    'name'       => 'Pedro',
                    'cpf'        => '85771330019',
                    'email'      => 'pedro@teste.com',
                    'office'     => 'DEV',
                    'parking_id' => 1,
                ],
                self::STATUS_CODE_CORRECT,
            ],
            'estacionamento-com-corpo-correto' => [
                [
                    'name'       => 'Giovanna',
                    'cpf'        => '95817965097',
                    'email'      => 'giovanna@teste.com',
                    'office'     => 'CEO',
                    'active'     => true,
                    'parking_id' => 1,
                ],
                self::STATUS_CODE_CORRECT,
            ],
        ];
    }

    /**
     * @dataProvider createOrUpdateEmployeesDataProvider
     */
    public function testCreateEmployees(array $requestData, int $expectedStatusCode): void
    {
        $response = $this->post(self::API_EMPLOYEES_PATH, $requestData, $this->UnauthenticatedHeader());

        $this->UnauthenticatedResponse($response);
    }

    /**
     * @dataProvider createOrUpdateEmployeesDataProvider
     */
    public function testUpdateEmployees(array $requestData, int $expectedStatusCode): void
    {
        $response = $this->patch(self::API_EMPLOYEES_PATH."/{$this->parking->id}/{$this->employees->id}", $requestData, $this->UnauthenticatedHeader());

        $this->UnauthenticatedResponse($response);
    }

    public function testGetParkingById(): void
    {
        $response = $this->get(self::API_EMPLOYEES_PATH."/{$this->parking->id}/{$this->employees->id}", $this->UnauthenticatedHeader());

        $this->UnauthenticatedResponse($response);
    }

    public function testDeleteById(): void
    {
        $response = $this->delete(self::API_EMPLOYEES_PATH."/{$this->parking->id}/{$this->employees->id}", [], $this->UnauthenticatedHeader());

        $this->UnauthenticatedResponse($response);
    }
}
