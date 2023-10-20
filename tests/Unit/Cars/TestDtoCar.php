<?php

namespace Tests\Unit\Cars;

use App\Dtos\Cars\CarsDTO;
use PHPUnit\Framework\TestCase;

class TestDtoCar extends TestCase
{

    public function testCriacaoDtoDeEntrada(): void
    {

        $carsDto = new CarsDTO(
            'NEJ1472',
            'Uno',
            'Verde',
            1,
        );



        $this->assertTrue(true);
    }
}
