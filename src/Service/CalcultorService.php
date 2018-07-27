<?php

namespace App\Service;

use App\Util\Calculator;
use Doctrine\Common\Persistence\ObjectManager;

class CalcultorService
{
    private $objectManager;

    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function getPrice()
    {
        $product = $this->objectManager->getRepository(Calculator::class);
    }
}