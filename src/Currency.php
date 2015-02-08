<?php
/**
 * Created by PhpStorm.
 * User: elliot
 * Date: 08/02/15
 * Time: 11:14 PM
 */

namespace Abacus;


class Currency {
    /**
     * @param Currency|string $currency
     */
    public function __construct($currency)
    {
        $this->rate = json_decode(file_get_contents(__DIR__.'/exchangerates.json'))->$currency;
    }
}