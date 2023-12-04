<?php

namespace App\Dtos\Reservations;

use App\Dtos\AbstractDTO;
use App\Dtos\InterfaceDTO;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="ReservationDTO",
 *     type="object",
 *
 *     @OA\Property(property="name", type="string", example="Estacionamento A"),
 *     @OA\Property(property="active", type="boolean", example=true),
 * )
 */
class ReservationDTO extends AbstractDTO implements InterfaceDTO
{
    public function __construct(
        public readonly int $parking_id,
        public readonly int $vacancy_id,
        public readonly int $car_id,
        public readonly string $start_date,
        public readonly ?string $end_date = null,
        public readonly int $status,
    ) {
        $this->validate();
    }

    public function rules(): array
    {
        return [
            'parking_id' => [
                'required',
                'int',
            ],

            'vacancy_id' => [
                'required',
                'int',
                Rule::unique('reservations'),
            ],

            'car_id' => [
                'required',
                'int',
                Rule::unique('reservations'),
            ],

            'start_date' => [
                'required',
                'date',
            ],

            'end_date' => [
                'nullable',
                'date',
            ],

            'status' => [
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
