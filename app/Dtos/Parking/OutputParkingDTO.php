<?php

namespace App\Dtos\Parking;

use App\Dtos\AbstractDTO;
use App\Dtos\InterfaceDTO;
use App\Models\Parking;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Collection;

/**
 * @OA\Schema(
 *     schema="OutputParkingDTO",
 *     type="object",
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Estacionamento A"),
 *     @OA\Property(property="numberOfVacancies", type="integer", example=50),
 *     @OA\Property(property="active", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="erro", type="boolean", example=false),
 *     @OA\Property(property="message", type="string", example=null),
 * )
 */
class OutputParkingDTO extends AbstractDTO implements InterfaceDTO
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $name = null,
        public readonly ?int $numberOfVacancies = null,
        public readonly ?bool $active = null,
        public readonly ?\DateTime $created_at = null,
        public readonly ?Collection $cars = null,
        public readonly ?Collection $employees = null,
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

    public static function fromModel(Parking $parking)
    {
        return new self(
            $parking->id,
            $parking->name,
            $parking->numberOfVacancies,
            $parking->active,
            $parking->created_at,
            $parking->cars,
            $parking->employees,
        );
    }
}
