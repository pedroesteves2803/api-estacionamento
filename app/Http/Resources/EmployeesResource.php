<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
