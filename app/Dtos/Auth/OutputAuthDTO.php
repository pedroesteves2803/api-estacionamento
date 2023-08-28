<?php

namespace App\Dtos\Auth;

use App\Dtos\AbstractDTO;
use App\Dtos\InterfaceDTO;
use Illuminate\Contracts\Validation\Validator;

/**
 * @OA\Schema(
 *     schema="OutputAuthDTO",
 *     type="object",
 *
 *     @OA\Property(property="token", type="string", example="1|dfsdsdfjhkdshkhdfskjhfjkshfksfhkdshfksjfh"),
 *     @OA\Property(property="erro", type="boolean", example=1),
 *     @OA\Property(property="message", type="string", example="Erro"),
 * )
 */
class OutputAuthDTO extends AbstractDTO implements InterfaceDTO
{
    public function __construct(
        public readonly ?string $token = null,
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
