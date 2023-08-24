<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ParkingResource",
 *     type="object",
 *
 *     @OA\Property(property="errors", type="boolean"),
 *     @OA\Property(property="message", type="string"),
 *     @OA\Property(
 *         property="content",
 *         type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="nome", type="string"),
 *         @OA\Property(property="numero_de_vagas", type="integer"),
 *         @OA\Property(property="ativo", type="boolean"),
 *         @OA\Property(property="criado", type="string", format="date"),
 *     ),
 * )
 */
class ParkingResource extends JsonResource
{
    public function toArray($request): array
    {
        $created_at = isset($this->created_at) ? Carbon::make($this->created_at)->format('d-m-Y') : null;

        return [
            'errors'  => isset($this->erro) ? $this->erro : null,
            'message' => isset($this->erro) ? $this->message : null,
            'content' => [
                'id'              => $this->id,
                'nome'            => $this->name,
                'numero_de_vagas' => $this->numberOfVacancies,
                'ativo'           => $this->active,
                'criado'          => $created_at,
                'carros'          => $this->cars,
                'funcionarios'    => $this->employees,
            ],
        ];
    }
}
