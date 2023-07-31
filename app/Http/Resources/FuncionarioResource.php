<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FuncionarioResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'cpf' => $this->cpf,
            'email' => $this->email,
            'cargo' => $this->cargo,
            'ativo' => $this->ativo,
            'estacionamento_id' => $this->estacionamento_id,
            'criado' => Carbon::make($this->created_at)->format('d-m-Y'),
        ];
    }
}
