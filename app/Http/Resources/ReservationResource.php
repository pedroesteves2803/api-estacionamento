<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $output = null;
        $input = !is_null($this->start_date) ? Carbon::make($this->start_date)->format('d-m-Y h:i:s') : null;

        if (isset($this->output) && !empty($this->output)) {
            $output = Carbon::make($this->end_date)->format('d-m-Y h:i:s');
        }

        return [
            'errors'  => isset($this->error) ? $this->error : null,
            'message' => isset($this->error) ? $this->message : null,
            'content' => [
                'id'                => $this->id,
                'estacionamento_id' => $this->parking_id,
                'vaga_id'           => $this->vacancy_id,
                'carro_id'          => $this->car_id,
                'entrada'           => $input,
                'saida'             => $output,
                'status'            => $this->status,
            ],
        ];
    }
}
