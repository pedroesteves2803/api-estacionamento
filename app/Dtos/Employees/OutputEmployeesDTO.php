<?php

namespace App\Dtos\Employees;

use App\Dtos\AbstractDTO;
use App\Dtos\InterfaceDTO;
use Illuminate\Contracts\Validation\Validator;

/**
 * @OA\Schema(
 *     schema="OutputEmployeesDTO",
 *     type="object",
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Pedro"),
 *     @OA\Property(property="cpf", type="string", example="123123123"),
 *     @OA\Property(property="email", type="string", example="exemplo@exemplo.com"),
 *     @OA\Property(property="office", type="string", example="CEO"),
 *     @OA\Property(property="active", type="boolean", example=true),
 *     @OA\Property(property="parking_id", type="integer", example=1),
 *     @OA\Property(property="erro", type="boolean", example=false),
 *     @OA\Property(property="message", type="string", example=null),
 * )
 */
class OutputEmployeesDTO extends AbstractDTO implements InterfaceDTO
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $name = null,
        public readonly ?string $cpf = null,
        public readonly ?string $email = null,
        public readonly ?string $office = null,
        public readonly ?bool $active = null,
        public readonly ?int $parking_id = null,
        public readonly ?bool $erro = false,
        public readonly ?string $message = null,
    ) {
        $this->validate();
    }

    public function rules(): array
    {
        return [];
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
