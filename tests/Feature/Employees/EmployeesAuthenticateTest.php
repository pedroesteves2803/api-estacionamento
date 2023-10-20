<?php

namespace Tests\Feature\Parkings;

use App\Models\Employees;
use App\Models\Parking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeesAuthenticateTest extends TestCase
{
    use RefreshDatabase;

    public const API_EMPLOYEES_PATH = '/api/employees';
    public const ERROR_MESSAGE = 'Registro não encontrado';
    public const PASSWORD = 'password';
    public const STATUS_CODE_CORRECT = 200;
    public const STATUS_CODE_ERROR = 401;

    protected $user;
    protected $parking;
    protected $employees;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        // arrange
        $this->user = User::factory()->create();
        $this->parking = Parking::factory()->create();
        $this->employees = Employees::factory()->create();
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

    public function testGetEmployees(): void
    {
        $response = $this->get(self::API_EMPLOYEES_PATH, $this->AuthHeaders());

        $response->assertStatus(self::STATUS_CODE_CORRECT)
            ->assertJsonStructure([
                'data' => [],
            ]);
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
        $response = $this->post(self::API_EMPLOYEES_PATH, $requestData, $this->AuthHeaders());

        $response->assertStatus($expectedStatusCode);

        $this->isFalse($response['data']['errors']);

        if (self::STATUS_CODE_CORRECT === $expectedStatusCode and false === $response['data']['errors']) {
            $this->assertDatabaseHas('employees', $requestData);
        } else {
            $this->assertEquals($response['data']['message'], self::ERROR_MESSAGE);
        }
    }

    /**
     * @dataProvider createOrUpdateEmployeesDataProvider
     */
    public function testUpdateEmployees(array $requestData, int $expectedStatusCode): void
    {
        $response = $this->patch(self::API_EMPLOYEES_PATH."/{$this->parking->id}/{$this->employees->id}", $requestData, $this->AuthHeaders());

        $response->assertStatus(self::STATUS_CODE_CORRECT);

        if (self::STATUS_CODE_CORRECT === $expectedStatusCode and false === $response['data']['errors']) {
            $this->assertDatabaseHas('employees', $requestData);
        } else {
            $this->assertEquals($response['data']['message'], self::ERROR_MESSAGE);
        }
    }

    public function testGetParkingById(): void
    {
        $response = $this->get(self::API_EMPLOYEES_PATH."/{$this->parking->id}/{$this->employees->id}", $this->AuthHeaders());

        $response->assertStatus(self::STATUS_CODE_CORRECT)
            ->assertJsonStructure([
                'data' => [],
            ]);

        $this->checkResponseBody($response);

        $content = $response['data']['content'];

        $this->assertEquals($this->employees->name, $content['nome']);
        $this->assertEquals($this->employees->cpf, $content['cpf']);
        $this->assertEquals($this->employees->email, $content['email']);
        $this->assertEquals($this->employees->office, $content['cargo']);
        $this->assertEquals($this->employees->active, $content['ativo']);
        $this->assertEquals($this->employees->parking_id, $content['estacionamento_id']);
    }

    public function testDeleteById(): void
    {
        $response = $this->delete(self::API_EMPLOYEES_PATH."/{$this->parking->id}/{$this->employees->id}", [], $this->AuthHeaders());

        $response->assertStatus(204);
    }
}
