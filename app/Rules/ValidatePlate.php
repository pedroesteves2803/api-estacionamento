<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;


class ValidatePlate implements ValidationRule
{
    public const mercosurStandardPlate = '/^[A-Z]{3}\d{1}[A-Z]{1}\d{2}$/';
    public const pivStandardPlate = '/^[A-Z]{3}\d{4}$/';

    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {

        if (!preg_match(self::mercosurStandardPlate, $value) && !preg_match(self::pivStandardPlate, $value)) {
            $fail('Placa não é válida!');
        }
    }
}
