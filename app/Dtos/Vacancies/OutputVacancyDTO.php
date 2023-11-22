<?php

namespace App\Dtos\Vacancies;

use App\Dtos\AbstractDTO;
use App\Dtos\InterfaceDTO;
use App\Models\Vacancy;
use Illuminate\Contracts\Validation\Validator;

/**
 * @OA\Schema(
 *     schema="OutputParkingDTO",
 *     type="object",
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Estacionamento A"),
 *     @OA\Property(property="active", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="erro", type="boolean", example=false),
 *     @OA\Property(property="message", type="string", example=null),
 * )
 */
class OutputVacancyDTO extends AbstractDTO implements InterfaceDTO
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?int $parking_id = null,
        public readonly ?int $number = null,
        public readonly ?bool $available = null,
        public readonly ?\DateTime $created_at = null,
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

    public static function fromModel(Vacancy $vacancy)
    {
        return new self(
            $vacancy->id,
            $vacancy->parking_id,
            $vacancy->number,
            $vacancy->available,
            $vacancy->created_at,
        );
    }
}
