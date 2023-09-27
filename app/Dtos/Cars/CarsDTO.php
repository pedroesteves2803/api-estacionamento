<?php

namespace App\Dtos\Cars;

use App\Dtos\AbstractDTO;
use App\Dtos\InterfaceDTO;
use Illuminate\Database\Query\Builder;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="CarsDTO",
 *     type="object",
 *
 *     @OA\Property(property="plate", type="string", example="ABC-123"),
 *     @OA\Property(property="model", type="string", example="Uno"),
 *     @OA\Property(property="color", type="string", example="Verde"),
 *     @OA\Property(property="parking_id", type="integer", example=1),
 * )
 */
class CarsDTO extends AbstractDTO implements InterfaceDTO
{
    public function __construct(
        public readonly string $plate,
        public readonly string $model,
        public readonly string $color,
        public readonly int $parking_id,
    ) {
        $this->validate();
    }

    public function rules(): array
    {
        return [
            'plate' => [
                'required',
                'string',
                Rule::unique('cars')->ignore(request()->id),
            ],

            'model' => [
                'required',
                'string',
            ],

            'color' => [
                'required',
                'string',
            ],

            'parking_id' => [
                'required',
                'int',
                Rule::exists('parkings', 'id')->where(function ($query) {
                    if(!empty(request()->input('parking_id'))){
                        $query->where('id', request()->input('parking_id'));
                    }
                }),
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
