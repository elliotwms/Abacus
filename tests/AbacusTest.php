<?php

require_once __DIR__."/../src/Abacus.php";

use Abacus\Abacus;

class AbacusTest extends PHPUnit_Framework_TestCase {

    /**
     * Instantiates
     *
     * Abacus object must instantiate with a value and a currency
     */
    public function testInstantiates()
    {
        $abacus = new Abacus();
        $this->assertObjectHasAttribute("value", $abacus);
        $this->assertObjectHasAttribute("currency", $abacus);
    }

    /**
     * Setting
     *
     * Should accept any integer or float
     */
    public function testSetsValue()
    {
        $this->assertAttributeEquals(1234.56, "value", new Abacus(1234.56));
        $this->assertAttributeEquals(1234.5678, "value", new Abacus(1234.5678));
    }

    /**
     * String typecasting
     *
     * __toString() magic method should be leveraged to produce a
     * pretty formatted result
     */
    public function testStringTypecast()
    {
        $this->assertEquals("1,234.56", strval(new Abacus(1234.56)));
    }

    /**
     * Formatting
     *
     * $this->format() should add the currency symbol to the string
     */
    public function testFormats()
    {
        $this->assertEquals("£1,234.56", (new Abacus(1234.56))->format());
        $this->assertEquals("$1,234.56", (new Abacus(1234.56, "USD"))->format());
    }

    /**
     * Addition
     *
     * Adding should convert the added value to the original currency and
     * add the values together. Fancy!
     *
     */
    public function testAddition()
    {
        // Adding integers
        $result = (new Abacus(1))->add(2);
        $this->assertEquals(3, $result->value);

        $result = (new Abacus(1))->add(1.5);
        $this->assertEquals(2.5, $result->value);

        // Adding Abacus objects
        $result = (new Abacus(1))->add(new Abacus(2));
        $this->assertEquals(3, $result->value);

        $result = (new Abacus(1))->add(new Abacus(1.5));
        $this->assertEquals(1.5, $result->value);
    }

    /**
     * Subtraction
     *
     * Exactly the same as addition, but the opposite
     *
     */
    public function testSubtraction()
    {
        // Subtracting numbers
        $result = (new Abacus(3))->sub(2);
        $this->assertEquals(1, $result->value);

        $result = (new Abacus(3))->sub(1.5);
        $this->assertEquals(1.5, $result->value);

        // Subtracting Abacus objects
        $result = (new Abacus(3))->sub(new Abacus(2));
        $this->assertEquals(1, $result->value);

        $result = (new Abacus(3))->sub(new Abacus(1.5));
        $this->assertEquals(1.5, $result->value);
    }

}