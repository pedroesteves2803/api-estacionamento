<?php

namespace App\Services\Utils;

use App\Exceptions\RequestFailureException;

class UtilsRequestService
{
    public function verifiedRequest(array $request, int $numberOfParameters)
    {

        if (empty($request) or count($request) < $numberOfParameters) {
            throw new RequestFailureException('Não foi possivel adicionar um novo carro!');
        }

        return true;
    }
}
