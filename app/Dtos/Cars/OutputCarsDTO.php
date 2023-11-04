<?php

namespace App\Dtos\Cars;

use App\Dtos\AbstractDTO;
use App\Dtos\InterfaceDTO;
use App\Models\Car;
use Illuminate\Contracts\Validation\Validator;

/**
 * @OA\Schema(
 *     schema="OutputCarsDTO",
 *     type="object",
 *
 *     @OA\Property(property="plate", type="string", example="ABC-123"),
 *     @OA\Property(property="model", type="string", example="Uno"),
 *     @OA\Property(property="color", type="string", example="Verde"),
 *     @OA\Property(property="input", type="string", example="2023-08-24", format="date"),
 *     @OA\Property(property="parking_id", type="integer", example=1),
 *     @OA\Property(property="output", type="string", example="2023-08-24", format="date"),
 *     @OA\Property(property="amountToBePaid", type="string", example="100"),
 *     @OA\Property(property="erro", type="boolean", example=1),
 *     @OA\Property(property="message", type="string", example="Erro"),
 * )
 */
class OutputCarsDTO extends AbstractDTO implements InterfaceDTO
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $plate = null,
        public readonly ?string $model = null,
        public readonly ?string $color = null,
        public readonly ?string $input = null,
        public readonly ?int $parking_id = null,
        public readonly ?string $output = null,
        public readonly ?string $amountToBePaid = null,
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

    public static function fromModel(Car $car)
    {
        return new self(
            $car->id,
            $car->plate,
            $car->model,
            $car->color,
            $car->input,
            $car->parking_id,
        );
    }
}
