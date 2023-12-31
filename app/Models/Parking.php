<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Parking extends Model
{
    use HasFactory;

    public $guarded = [
        'id',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function cars(): HasMany
    {
        return $this->hasMany(Car::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employees::class);
    }

    public static function hoursToMinutes($horas)
    {
        return $horas * 60;
    }

    public static function daysToMinutes($dias)
    {
        return $dias * 24 * 60;
    }

    public static function calculateParkingValue($parkedMinutes)
    {
        return (float) $parkedMinutes * 0.20;
    }

    public static function getAmountToPay(Carbon $input, Carbon $output)
    {
        $result = $input->diff($output);

        $days = $result->d;
        $hours = $result->h;
        $minutes = $result->i;
        $total = 0;

        if (0 != $days) {
            $total += self::daysToMinutes($days);
        }

        if (0 != $hours) {
            $total += self::hoursToMinutes($hours);
        }

        $total += $minutes;

        return number_format(self::calculateParkingValue($total), 2, ',', '.');
    }
}
