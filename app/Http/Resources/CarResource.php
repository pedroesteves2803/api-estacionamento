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
        $input = !is_null($this->input) ? Carbon::make($this->input)->format('d-m-Y h:i:s') : null;

        if (isset($this->output) && !empty($this->output)) {
            $output = Carbon::make($this->output)->format('d-m-Y h:i:s');
            $amount_to_pay = Parking::getAmountToPay(Carbon::make($this->input), Carbon::make($this->output));
        }

        return [
            'errors'  => isset($this->erro) ? $this->erro : null,
            'message' => isset($this->erro) ? $this->message : null,
            'content' => [
                'id'                   => $this->id,
                'placa'                => $this->plate,
                'modelo'               => $this->model,
                'cor'                  => $this->color,
                'entrada'              => $input,
                'estacionamento_id'    => $this->parking_id,
                'saida'                => $output,
                'valor_para_pagamento' => $amount_to_pay,
            ],
        ];
    }
}
