<?php
/**
 * Created by PhpStorm.
 * User: elliot
 * Date: 08/02/15
 * Time: 1:08 PM
 */

namespace Abacus;

use Abacus\Currency;

abstract class BaseCurrency
{
    public $symbol = '?';
    public $name = 'BCU';
    public $decimalUnit = '.';
    public $thousands = ',';
    public $rate = 1;

    public function __toString()
    {
        return $this->name;
    }
}

class GBP extends BaseCurrency
{
    public $name = "GBP";
    public $description = "Great British Pounds";
    public $symbol = "£";
    public $rate = 0.656179;

}

class USD extends BaseCurrency
{
    public $name = "USD";
    public $description = "United States Dollars";
    public $symbol = "$";
    public $rate = 1;

}

class BTC extends BaseCurrency
{
    public $name = "BTC";
    public $description = "Bitcoins";
    public $symbol = "Ƀ";
    public $rate = 0.0044925386;

}

class IMC extends BaseCurrency
{
    public $name = "IMC";
    public $description = "IMaginary Currency";
    public $rate = 2;
}

class Abacus {

    /**
     * Create a new Abacus object
     *
     * @param float $value
     * @param string|null $currency
     */
    public function __construct($value = null, $currency = "GBP")
    {
        if (is_null($currency)) {
            $currency = new GBP;
        } else {
            $currency = "Abacus\\$currency";
            $currency = new $currency;
        }
        $this->value = $value;
        $this->currency = $currency;
    }

    public function __toString()
    {
        return number_format($this->value, 2, $this->currency->decimalUnit, $this->currency->thousands);
    }

    public function format()
    {
        return $this->currency->symbol . $this->__toString();
    }

    /**
     * Add two currencies together.
     *
     * Either add a float to an Abacus object, or add another Abacus object
     * of any currency to the current abacus object. Either way, this results
     * in an Abacus object of the original currency
     *
     * @param Abacus|float $value
     * @param string|null $currency
     * @return $this
     */
    public function add($value, $currency = null)
    {
        if (is_a($value, "Abacus\\Abacus")) {
            // If adding an Abacus object
            // Convert the object to the current object's currency and
            // add it to the current object
            $this->value += $value->to($this->currency->name)->value;
        } else {
            if (isset($currency)) {
                $value = new Abacus($value, $currency);
            } else {
                $value = new Abacus($value, $this->currency->name);
            }
            return $this->add($value);
        }

        // Return the object
        return $this;
    }

    /**
     * Subtract from a currency
     *
     * Subtract an Abacus object or a float from an Abacus object. The
     * exact opposite of the ->add() function, but with a cheeky
     * reversed sign
     *
     * @param Abacus|float $value
     * @param string|null $currency
     * @return Abacus
     */
    public function sub($value, $currency = null)
    {
        // Cheeky cheeky
        if (is_a($value, "Abacus\\Abacus")) {
            return $this->add(-$value->value, $currency);
        }
        return $this->add(-$value, $currency);
    }

    /**
     * Convert to a currency
     *
     * Convert the Abacus object from one currency to another.
     *
     * @param string $currency
     * @return $this
     */
    public function to($currency)
    {
        // Get the new Currency Model
        $currency = "Abacus\\$currency";
        $currency = new $currency;

        // New currency = Current / rate * new rate
        $this->value = $this->value / $this->currency->rate * $currency->rate;

        // Update the currency of the Abacus model
        $this->currency = new $currency;

        return $this;
    }

}