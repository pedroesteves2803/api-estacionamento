<?php

namespace Tests\Unit\UtilsRequest;

use App\Services\Utils\UtilsRequestService;
use PHPUnit\Framework\TestCase;

class UtilsRequestTest extends TestCase
{

    const STATUS_CORRECT = true;
    const STATUS_INCORRECT = false;

    /**
     * Data Provider para testar diferentes cenários de criação de estacionamento.
     */
    public static function RequestsDataProvider()
    {
        return [
            'pasando-dois-parametros-esperando-dois' => [
                [
                    'name' => 'Estacionamento de sucesso 0',
                    'numberOfVacancies' => 50
                ],
                2,
                self::STATUS_CORRECT,
            ],
            'pasando-dois-parametros-esperando-tres' => [
                [
                    'name' => 'Estacionamento de sucesso 1',
                    'numberOfVacancies' => 100,
                ],
                3,
                self::STATUS_INCORRECT,
            ]
        ];
    }

    /**
     * @dataProvider RequestsDataProvider
     */
    public function testVerifiedRequestIsCorrect(array $requestData, int $numberOfParameters, bool $expectedStatus): void
    {
        $response = (new UtilsRequestService())->verifiedRequest($requestData, $numberOfParameters);

        if($expectedStatus){
            $this->assertFalse($response);
        }else{
            $this->assertTrue($response);
        }
    }
}
