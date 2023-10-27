<?php

namespace Tests\Unit\UtilsRequest;

use App\Exceptions\RequestFailureException;
use App\Services\Utils\UtilsRequestService;
use PHPUnit\Framework\TestCase;

class UtilsRequestTest extends TestCase
{

    const STATUS_CORRECT = true;
    const STATUS_INCORRECT = false;

    public static function RequestsDataProviderIncorrect()
    {
        return [
            'pasando-dois-parametros-esperando-tres' => [
                [
                    'name' => 'Estacionamento de sucesso 0',
                    'numberOfVacancies' => 50
                ],
                3,
            ]
        ];
    }

    public static function RequestsDataProviderCorrect()
    {
        return [
            'pasando-dois-parametros-esperando-dois' => [
                [
                    'name' => 'Estacionamento de sucesso 0',
                    'numberOfVacancies' => 50
                ],
                2,
                self::STATUS_CORRECT,
            ]
        ];
    }

    /**
     * @dataProvider RequestsDataProviderIncorrect
     */
    public function testVerifiedRequestIsIncorrect(array $requestData, int $numberOfParameters): void
    {
        $this->expectException(RequestFailureException::class);
        $this->expectExceptionMessage('Não foi possivel adicionar um novo carro!');

        (new UtilsRequestService())->verifiedRequest($requestData, $numberOfParameters);

    }

    /**
     * @dataProvider RequestsDataProviderCorrect
     */
    public function testVerifiedRequestIsCorrect(array $requestData, int $numberOfParameters): void
    {
        $response = (new UtilsRequestService())->verifiedRequest($requestData, $numberOfParameters);

        $this->assertTrue($response);

    }
}
