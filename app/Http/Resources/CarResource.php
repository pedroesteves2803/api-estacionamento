<?php

namespace App\Http\Resources;

use App\Models\Parking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $output = null;
        $amount_to_pay = null;

        if (isset($this->output) && !empty($this->output)) {
            $output = Carbon::make($this->output)->format('d-m-Y h:i:s');
            $amount_to_pay = Parking::getAmountToPay(Carbon::make($this->input), Carbon::make($this->output));
        }

        return [
            'id'                   => $this->id,
            'placa'                => $this->plate,
            'modelo'               => $this->model,
            'cor'                  => $this->color,
            'entrada'              => Carbon::make($this->input)->format('d-m-Y h:i:s'),
            'estacionamento_id'    => $this->parking_id,
            'saida'                => $output,
            'valor_para_pagamento' => $amount_to_pay,
        ];
    }
}
