<?php

use unapi\def\tele2\Tele2Service;
use unapi\dto\PhoneDto;

class Tele2Test extends \PHPUnit_Framework_TestCase
{
    public function testDetection()
    {
        $service = new Tele2Service();

        $this->assertEquals(
            $service->detectOperator(new PhoneDto('9152324203'))->wait(),
            ['name' => 'Tele2', 'mnc' => 25020]
        );
    }
}