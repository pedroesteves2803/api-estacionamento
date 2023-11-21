<?php

namespace App\Dtos\Parking;

use App\Dtos\AbstractDTO;
use App\Dtos\InterfaceDTO;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="ParkingDTO",
 *     type="object",
 *
 *     @OA\Property(property="name", type="string", example="Estacionamento A"),
 *     @OA\Property(property="active", type="boolean", example=true),
 * )
 */
class VacancyDTO extends AbstractDTO implements InterfaceDTO
{
    public function __construct(
        public readonly string $name,
        public readonly bool $active,
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

            'number' => [
                'required',
                'int',
            ],

            'available' => [
                'required',
                'boolean',
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
