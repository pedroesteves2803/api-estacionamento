<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EstacionamentoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'quantidadeDeVagas' => $this->quantidadeDeVagas,
            'ativo' => $this->ativo,
            'criado' => Carbon::make($this->created_at)->format('d-m-Y'),
            'carros' => $this->carros
        ];
    }
}
