<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ParkingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'              => $this->id,
            'nome'            => $this->name,
            'numero_de_vagas' => $this->numberOfVacancies,
            'ativo'           => $this->active,
            'criado'          => Carbon::make($this->created_at)->format('d-m-Y'),
            'carros'          => $this->cars,
            'funcionarios'    => $this->employees,
        ];
    }
}
