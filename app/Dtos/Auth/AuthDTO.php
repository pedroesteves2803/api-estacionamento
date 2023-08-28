<?php

namespace App\Dtos\Auth;

use App\Dtos\AbstractDTO;
use App\Dtos\InterfaceDTO;
use Illuminate\Contracts\Validation\Validator;

/**
 * @OA\Schema(
 *     schema="AuthDTO",
 *     type="object",
 *
 *     @OA\Property(property="email", type="string", example="teste@example.com"),
 *     @OA\Property(property="password", type="string", example="12345678"),
 *     @OA\Property(property="device_name", type="string", example="Test"),
 * )
 */
class AuthDTO extends AbstractDTO implements InterfaceDTO
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
        public readonly string $device_name,
    ) {
        $this->validate();
    }

    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'string',
                'email',
            ],

            'password' => [
                'required',
                'string',
            ],

            'device_name' => [
                'required',
                'string',
            ],
        ];
    }

    public function messages(): array
    {
        return [];
    }

    public function validator(): Validator
    {
        return validator($this->toArray(), $this->rules(), $this->messages());
    }

    public function validate(): array
    {
        return $this->validator()->validate();
    }
}
