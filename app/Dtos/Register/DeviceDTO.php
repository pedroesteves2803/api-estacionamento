<?php

namespace App\Dtos\Register;

use App\Dtos\AbstractDTO;
use App\Dtos\InterfaceDTO;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="DeviceDTO",
 *     type="object",
 *
 *     @OA\Property(property="device_name", type="string", example="teste"),
 *     @OA\Property(property="email", type="string", example="teste@example.com"),
 *     @OA\Property(property="password", type="string", example="12345678"),
 *     @OA\Property(property="password_confirmation", type="string", example="12345678"),
 * )
 */
class DeviceDTO extends AbstractDTO implements InterfaceDTO
{
    public function __construct(
        public readonly string $device_name,
        public readonly string $email,
        public readonly string $password,
        public readonly string $password_confirmation
    ) {
        $this->validate();
    }

    public function rules(): array
    {
        return [
            'device_name' => [
                'required',
                'string',
            ],

            'email' => [
                'required',
                'string',
                'email',
                Rule::unique('users')->ignore(request()->id),
            ],

            'password' => [
                'required',
                'string',
                'confirmed',
            ],

            'password_confirmation' => [
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
