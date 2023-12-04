<?php

namespace App\Tests\Entity;

use App\Entity\Measurement;
use PHPUnit\Framework\TestCase;

class MeasurementTest extends TestCase
{
    public function dataGetFahrenheit()
{
    return [
        [0, 32],
        [-100, -148],
        [100, 212],
        [25, 77],
        [50, 122],
        [75, 167],
        [-10, 14],
        [10, 50],
        [-5, 23],
        [30, 86],
    ];
}


    /**
     * @dataProvider dataGetFahrenheit
     */
    public function testGetFahrenheit($celsius, $expectedFahrenheit): void
    {
        $measurement = new Measurement();

        $measurement->setCelsius($celsius);
        $fahrenheit = $measurement->getFahrenheit();
        $this->assertEquals($fahrenheit, $expectedFahrenheit);
    }
}
