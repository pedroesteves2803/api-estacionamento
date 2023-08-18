<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;

    public $guarded = [
        'id',
    ];

    protected $casts = [
        'input'  => 'datetime:d-m-Y h:i:s',
        'output' => 'datetime:d-m-Y h:i:s',
    ];
}
