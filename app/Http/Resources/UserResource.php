<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="UserResource",
 *     type="object",
 *
 *     @OA\Property(property="errors", type="boolean"),
 *     @OA\Property(property="message", type="string"),
 *     @OA\Property(
 *         property="content",
 *         type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="nome_do_dispositivo", type="string"),
 *         @OA\Property(property="email", type="string"),
 *     ),
 * )
 */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'errors'  => isset($this->erro) ? $this->erro : null,
            'message' => $this->message ?? null,
            'content' => [
                'id'                  => $this->id,
                'nome_do_dispositivo' => $this->device_name,
                'email'               => $this->email,
            ],
        ];
    }
}
