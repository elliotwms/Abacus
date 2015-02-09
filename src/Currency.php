<?php

namespace Abacus;

/**
 * Class Currency
 * @package Abacus
 */
class Currency {
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
        $this->symbol = $currency->symbol;
        $this->description = $currency->description;
        $this->decimal_separator = $currency->decimal_separator;
        $this->thousands_separator = $currency->thousands_separator;

        // Get the DateTime for when the exchange was updated last
        $this->updated_at = $exchange->updated;
    }
}