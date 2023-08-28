<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="AuthResource",
 *     type="object",
 *
 *     @OA\Property(property="errors", type="boolean"),
 *     @OA\Property(property="message", type="string"),
 *     @OA\Property(
 *         property="content",
 *         type="object",
 *         @OA\Property(property="token", type="string"),
 *     ),
 * )
 */
class AuthResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'errors'  => isset($this->erro) ? $this->erro : null,
            'message' => isset($this->erro) ? $this->message : null,
            'content' => [
                'token' => isset($this->token) ? $this->token : null,
            ],
        ];
    }
}
