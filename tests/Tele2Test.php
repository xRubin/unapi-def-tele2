<?php

use unapi\def\tele2\Tele2Service;
use unapi\def\common\dto\PhoneDto;
use unapi\def\common\dto\OperatorDto;

class Tele2Test extends \PHPUnit_Framework_TestCase
{
    public function testDetection()
    {
        $service = new Tele2Service();

        $this->assertEquals(
            $service->detectOperator(new PhoneDto('9152324203'))->wait(),
            OperatorDto::toDto(['name' => 'Tele2', 'mnc' => 25020])
        );
    }
}