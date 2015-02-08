<?php

use Abacus\Abacus;

class AbacusTest extends PHPUnit_Framework_TestCase {

    public function testInstantiates()
    {
        $value = new Abacus();
        $this->assertTrue($value);
    }

}