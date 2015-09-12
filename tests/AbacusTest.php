<?php

require_once(__DIR__ . "/../vendor/autoload.php");

use Abacus\Abacus;
use Abacus\Currency;

class AbacusTest extends PHPUnit_Framework_TestCase
{

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
        $this->assertEquals("1234.56", strval(new Abacus(1234.56)));
        $this->assertEquals("1234.567", strval(new Abacus(1234.567)));
        $this->assertEquals("1234.5678", strval(new Abacus(1234.5678)));
    }

    /**
     * Formatting
     *
     * $this->format() should add the currency symbol to the string
     */
    public function testFormats()
    {
        // £GBP by default
        $this->assertEquals("$1,234.56", (new Abacus(1234.56))->format());

        // Created as $USD
        $this->assertEquals("£1,234.56", (new Abacus(1234.56, "GBP"))->format());
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
        $this->assertEquals(2.5, $result->value);

        $result = (new Abacus(1))->add(new Abacus(2, "USD"));
        $this->assertEquals(3, $result->value);

        $currency = new Currency();
        $result = (new Abacus(1))->add(new Abacus(2, $currency));
        $this->assertEquals(3, $result->value);
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

        $result = (new Abacus(1))->sub(new Abacus(2, "GBP"));
        $this->assertEquals(-1, $result->value);

        $currency = new Currency("GBP");
        $result = (new Abacus(1))->sub(new Abacus(2, $currency));
        $this->assertEquals(-1, $result->value);
    }

    /**
     * Immutability
     *
     * Addition and subtraction should be immutable, IE not
     * changing the Abacus objects themselves but producing
     * new ones to play with
     */
    public function testImmutability()
    {
        $a = new Abacus(100);
        $b = $a->add(200);

        $this->assertEquals(100, $a->value);
        $this->assertEquals(300, $b->value);

        $c = new Abacus(300);
        $d = $c->sub(200);

        $this->assertEquals(300, $c->value);
        $this->assertEquals(100, $d->value);

        // Should also work with chaining
        $e = new Abacus(1);
        $f = $e->add(1)->add(1)->add(1);

        $this->assertEquals(1, $e->value);
        $this->assertEquals(4, $f->value);
    }

}