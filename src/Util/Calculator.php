<?php

namespace App\Util;

class Calculator
{
    private $title;

    public function __construct()
    {
        $this->title = 'chien';
    }

    public function add($a, $b)
    {
        return $a + $b;
    }
}