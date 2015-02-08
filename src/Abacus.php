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
}

class GBP extends BaseCurrency
{
    public $symbol = "£";

}

class USD extends BaseCurrency
{
    public $symbol = "$";

}

class BTC extends BaseCurrency
{
    public $symbol = "Ƀ";

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

}