<?php

namespace App\Tests\Util;

use App\Util\Calculator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CalculatorTest extends KernelTestCase
{
    public function testAdd()
    {
        $calculator = new Calculator();
        $result     = $calculator->add(30, 12);

        $this->assertEquals(42, $result);
    }

    public function testMock()
    {
        $calculator = $this->createMock(Calculator::class);
        //var_dump($calculator); die;
        //$calculator = $this->getMockBuilder(Calculator::class)->getMock();
        //var_dump($calculator); die;
//        $calculator->expects($this->any())
//                   ->method('add')
//                   ->willReturn(3);

        $mockedAddition = [
            [2, 2, 10],
            [2, 3, 11]
        ];

        $calculator->expects($this->any())
                   ->method('add')
                   ->will($this->returnValueMap($mockedAddition));

        $this->assertEquals(null, $calculator->add(4, 5));
    }
}