<?php

namespace App\Http\Resources;

use App\Models\Parking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="CarResource",
 *     type="object",
 *
 *     @OA\Property(property="errors", type="boolean"),
 *     @OA\Property(property="message", type="string"),
 *     @OA\Property(
 *         property="content",
 *         type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="placa", type="string"),
 *         @OA\Property(property="modelo", type="strign"),
 *         @OA\Property(property="cor", type="string"),
 *         @OA\Property(property="entrada", type="string", format="date"),
 *         @OA\Property(property="estacionamento_id", type="integer"),
 *         @OA\Property(property="saida", type="string", format="date"),
 *         @OA\Property(property="valor_para_pagamento", type="string"),
 *     ),
 * )
 */
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
