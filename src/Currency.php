<?php

namespace Abacus;

/**
 * Class Currency
 * @package Abacus
 */
class Currency
{
    /**
     * __construct
     *
     * Create a new Currency object
     *
     * @param string $currency The currency's ISO code
     */
    public function __construct($currency)
    {
        // Get the Currency object from the exchange.json file
        $exchange = json_decode(file_get_contents(__DIR__ . '/../storage/exchange.json'));

        // Transform the $currency variable into an instance of
        // currency object from the ol' JSON file
        $currency = $exchange->currencies->$currency;

        // Get the attributes from the currency JSON object
        $this->rate = $currency->rate;
        $this->decimal_separator = '.';
        $this->thousands_separator = ',';

        $this->symbol = $currency->symbol;
        $this->name = $currency->name;
        $this->symbol_native = $currency->symbol_native;
        $this->decimal_digits = $currency->decimal_digits;
        $this->rounding = $currency->rounding;
        $this->code = $currency->code;
        $this->name_plural = $currency->name_plural;

        // Get the DateTime for when the exchange was updated last
        $this->updated_at = $exchange->updated;
    }
}