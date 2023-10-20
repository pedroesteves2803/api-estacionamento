<?php

namespace App\Services\Utils;

class UtilsRequestService
{
    public function verifiedRequest(array $request, int $numberOfParameters)
    {
        return count($request) < $numberOfParameters;
    }

    // Outros métodos comuns podem ser adicionados aqui
}
