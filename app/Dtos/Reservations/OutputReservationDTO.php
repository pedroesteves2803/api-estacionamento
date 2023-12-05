<?php

namespace App\Dtos\Reservations;

use App\Dtos\AbstractDTO;
use App\Dtos\InterfaceDTO;
use App\Models\Reservations;
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
class OutputReservationDTO extends AbstractDTO implements InterfaceDTO
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?int $parking_id = null,
        public readonly ?int $vacancy_id = null,
        public readonly ?int $car_id = null,
        public readonly ?string $start_date = null,
        public readonly ?string $end_date = null,
        public readonly ?int $status = null,
        public readonly ?bool $error = false,
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

    public static function fromModel(Reservations $reservation)
    {
        return new self(
            $reservation->id,
            $reservation->parking_id,
            $reservation->vacancy_id,
            $reservation->car_id,
            $reservation->start_date,
            $reservation->end_date,
            $reservation->status,
        );
    }
}
