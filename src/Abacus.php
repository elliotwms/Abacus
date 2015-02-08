<?php
/**
 * Created by PhpStorm.
 * User: elliot
 * Date: 08/02/15
 * Time: 1:08 PM
 */

namespace Abacus;

abstract class BaseCurrency
{
    public $symbol = '?';
    public $decimalUnit = '.';
    public $thousands = ',';
    public $rate = 1;
}

class GBP extends BaseCurrency
{
    public $symbol = "£";
    public $rate = 0.656179;

}

class USD extends BaseCurrency
{
    public $symbol = "$";
    public $rate = 1;

}

class BTC extends BaseCurrency
{
    public $symbol = "Ƀ";
    public $rate = 0.0044925386;

}

class IMC extends BaseCurrency
{
    public $rate = 2;
}

class Abacus {

    /**
     * Create a new Abacus object
     *
     * @param float $value
     * @param string|null $currency
     */
    public function __construct($value = null, $currency = null)
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