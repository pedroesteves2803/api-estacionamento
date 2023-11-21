<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vacancy extends Model
{
    use HasFactory;

    protected $table = 'vacancies';

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'start_date'  => 'datetime:d-m-Y h:i:s',
        'end_date' => 'datetime:d-m-Y h:i:s',
    ];
}
