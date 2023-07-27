<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carro extends Model
{
    use HasFactory;

    public $fillable = [
        'placa',
        'modelo',
        'cor',
        'estacionamento_id',
        'entrada',
        'saida',
    ];

    protected $casts = [
        'entrada' => 'datetime:d-m-Y h:i:s',
        'saida' => 'datetime:d-m-Y h:i:s',
    ];
}
