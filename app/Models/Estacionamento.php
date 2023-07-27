<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function carros(): HasMany
    {
        return $this->hasMany(Carro::class);
    }
}
