<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeesResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->name,
            'cpf' => $this->cpf,
            'email' => $this->email,
            'cargo' => $this->office,
            'ativo' => $this->active,
            'estacionamento_id' => $this->parking_id,
        ];
    }
}
