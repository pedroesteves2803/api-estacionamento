<?php

namespace Tests\Feature\Device;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeviceTest extends TestCase
{
    use RefreshDatabase;

    public const API_REGISTER_PATH = '/api/register/device';
    public const ERROR_MESSAGE = 'Registro não encontrado';
    public const STATUS_CODE_CORRECT = 200;
    public const STATUS_CODE_ERROR = 401;

    private function Headers(): array
    {
        return [
            'Accept' => 'application/json',
        ];
    }

    /**
     * Data Provider para testar diferentes cenários de criação de devices.
     */
    public static function createOrUpdateDeviceDataProvider()
    {
        return [
            'device' => [
                [
                    'device_name'           => 'postman',
                    'email'                 => 'test@gmail.com.br',
                    'password'              => 'password',
                    'password_confirmation' => 'password',
                ],
                self::STATUS_CODE_CORRECT,
            ],
        ];
    }

    /**
     * @dataProvider createOrUpdateDeviceDataProvider
     */
    public function testCreateDevice(array $requestData, int $expectedStatusCode): void
    {
        $response = $this->post(self::API_REGISTER_PATH, $requestData, $this->Headers());

        $response->assertStatus($expectedStatusCode);

        $this->isFalse($response['data']['errors']);

        if (self::STATUS_CODE_CORRECT === $expectedStatusCode and false === $response['data']['errors']) {
            $requestDataCorrect = [
                'device_name' => $requestData['device_name'],
                'email'       => $requestData['email'],
            ];

            $this->assertDatabaseHas('users', $requestDataCorrect);
        } else {
            $this->assertEquals(self::ERROR_MESSAGE, $response['data']['message']);
        }
    }
}
