<?php

use unapi\dto\PhoneDto;
use unapi\def\tele2\Tele2Service;
use unapi\def\common\dto\OperatorDto;

class Tele2Test extends \PHPUnit_Framework_TestCase
{
    public function testDetection()
    {
        $service = new Tele2Service();

        $this->assertEquals(
            $service->detectOperator(new PhoneDto('9152324203'))->wait(),
            (new OperatorDto())->setMnc(25020)->setName('Tele2')
        );
    }
}