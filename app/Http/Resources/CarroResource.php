<?php

namespace App\Http\Resources;

use App\Models\Carro;
use App\Models\Estacionamento;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarroResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $saida = null;

        if(isset($this->saida) && empty($this->saida)){
            $saida = Carbon::make($this->saida)->format('d-m-Y h:i:s');
        }

        return [
            'id' => $this->id,
            'placa' => $this->placa,
            'modelo' => $this->modelo,
            'cor' => $this->cor,
            'entrada' => Carbon::make($this->entrada)->format('d-m-Y h:i:s'),
            'estacionamento_id' => $this->estacionamento_id,
            'saida' => Carbon::make($this->saida)->format('d-m-Y h:i:s'),
            'valor_para_pagamento' => Estacionamento::getAmountToPay(Carbon::make($this->entrada), Carbon::make($this->saida))
        ];
    }
}
