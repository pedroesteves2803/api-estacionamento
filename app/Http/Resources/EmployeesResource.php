<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="EmployeesResource",
 *     type="object",
 *
 *     @OA\Property(property="errors", type="boolean"),
 *     @OA\Property(property="message", type="string"),
 *     @OA\Property(
 *         property="content",
 *         type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="nome", type="string"),
 *         @OA\Property(property="cpf", type="string"),
 *         @OA\Property(property="email", type="string"),
 *         @OA\Property(property="cargo", type="string"),
 *         @OA\Property(property="ativo", type="boolean"),
 *         @OA\Property(property="estacionamento_id", type="integer"),
 *     ),
 * )
 */
class EmployeesResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'errors'  => isset($this->erro) ? $this->erro : null,
            'message' => isset($this->erro) ? $this->message : null,
            'content' => [
                'id'                => $this->id,
                'nome'              => $this->name,
                'cpf'               => $this->cpf,
                'email'             => $this->email,
                'cargo'             => $this->office,
                'ativo'             => $this->active,
                'estacionamento_id' => $this->parking_id,
            ],
        ];
    }
}
