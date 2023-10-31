<?php

namespace App\Services\Utils;

use App\Exceptions\RequestFailureException;

class UtilsRequestService
{
    public function verifiedRequest(array $request, int $numberOfParameters)
    {

        if (empty($request) or count($request) < $numberOfParameters) {
            throw new RequestFailureException('Request incorreto!');
        }

        return true;
    }
}
