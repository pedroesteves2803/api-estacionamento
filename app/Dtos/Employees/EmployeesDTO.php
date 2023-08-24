<?php

namespace App\Dtos\Employees;

use App\Dtos\AbstractDTO;
use App\Dtos\InterfaceDTO;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="EmployeesDTO",
 *     type="object",
 *
 *     @OA\Property(property="name", type="string", example="Pedro"),
 *     @OA\Property(property="cpf", type="string", example="123123123"),
 *     @OA\Property(property="email", type="string", example="exemplo@exemplo.com"),
 *     @OA\Property(property="office", type="string", example="CEO"),
 *     @OA\Property(property="active", type="boolean", example=true),
 *     @OA\Property(property="parking_id", type="integer", example=1),
 * )
 */
class EmployeesDTO extends AbstractDTO implements InterfaceDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $cpf,
        public readonly string $email,
        public readonly string $office,
        public readonly bool $active,
        public readonly int $parking_id,
    ) {
        $this->validate();
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
            ],

            'cpf' => [
                'required',
                'string',
                Rule::unique('employees')->ignore(request()->id),
            ],

            'email' => [
                'required',
                'string',
                Rule::unique('employees')->ignore(request()->id),
            ],

            'office' => [
                'required',
                'string',
            ],

            'active' => [
                'required',
                'boolean',
            ],

            'parking_id' => [
                'required',
                'int',
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
