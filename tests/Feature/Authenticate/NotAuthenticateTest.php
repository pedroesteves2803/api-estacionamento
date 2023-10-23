<?php

namespace Tests\Feature\Parkings;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotAuthenticateTest extends TestCase
{
    use RefreshDatabase;

    public const API_LOGIN_PATH = '/api/login';
    public const UNAUTHENTICATED_MESSAGE = 'Usuário não encontrado!';
    public const PASSWORD = 'password_incorrect';
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
    }

    private function checkResponseBody($response): void
    {
        $this->assertNotNull($response['data']);
        $this->assertEquals(true, $response['data']['errors']);
        $this->assertEquals(self::UNAUTHENTICATED_MESSAGE, $response['data']['message']);
        $this->assertIsArray($response['data']['content']);
    }

    public function testNotLogin()
    {
        $body = [
            'email'    => $this->user->email,
            'password' => self::PASSWORD,
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post(self::API_LOGIN_PATH, $body);

        $this->checkResponseBody($response);

        $response->assertStatus(self::STATUS_CODE_CORRECT);
        $this->assertNull($response['data']['content']['token']);
    }
}
