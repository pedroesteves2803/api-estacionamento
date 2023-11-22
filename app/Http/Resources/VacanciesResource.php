<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VacanciesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $created_at = isset($this->created_at) ? Carbon::make($this->created_at)->format('d-m-Y') : null;

        return [
            'errors'  => isset($this->erro) ? $this->erro : null,
            'message' => isset($this->erro) ? $this->message : null,
            'content' => [
                'id'                => $this->id,
                'estacionamento_id' => $this->parking_id,
                'numero'            => $this->number,
                'disponivel'        => $this->available,
                'criado'            => $created_at,
            ],
        ];
    }
}
