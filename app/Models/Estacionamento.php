<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estacionamento extends Model
{
    use HasFactory;

    public $fillable = [
        'nome',
        'quantidadeDeVagas',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];
}
