<?php

namespace Abacus;

/**
 * Class Currency
 * @package Abacus
 */
class Currency
{
    public $rate = 1;
    public $decimal_separator = '.';
    public $thousands_separator = ',';
    public $symbol = '';
    public $name = '';
    public $symbol_native = '';
    public $decimal_digits = 2;
    public $rounding;
    public $code;
    public $name_plural;

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

    /**
     * Update Abacus's exchange rates
     *
     * Update the contents of storage/exchange.json by polling the
     * OpenExchangeRates API, getting the currencies in currencies.json
     * and combining the two.
     *
     * @param string|null $key API Key
     *
     * @return int|false The number of bytes written to file (or false)
     *
     */
    public static function update($key = null)
    {
        $latest = self::_fetchAPI($key);

        $currencies = self::_getCurrencies();

        foreach ($currencies as $name => &$currency) {
            if (isset($latest->rates->$name)) {
                $currency->rate = $latest->rates->$name;
            } else {
                unset($currencies->$name);
            }
        }

        return self::_setCurrencies($currencies, $latest->timestamp);
    }

    /**
     * Fetch API
     *
     * Get the exchange rates from the API. If
     * no API key is supplied, it will look for one as an environment
     * variable.
     *
     * @param $key
     *
     * @throws AbacusException
     *
     * @return array|false
     */
    private static function _fetchAPI($key)
    {
        if (is_null($key)) {
            $key = getenv('ABACUS_OPEN_EXCHANGE_KEY');
        }

        $url = "http://openexchangerates.org/api/latest.json?app_id=$key";

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_USERAGENT => "Abacus",
            CURLOPT_AUTOREFERER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_MAXREDIRS => 10
        ]);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            return false;
        };

        // Must have a correct API key in order to function properly
        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) === 401) {
            throw new AbacusException("Incorrect or undefined API key");
        }

        return json_decode($result);
    }

    /**
     * Get Currencies
     *
     * Get the contents of the currencies.json file and decode it
     * into a stdClass object
     *
     * @return object|false
     */
    private static function _getCurrencies()
    {
        return json_decode(file_get_contents(__DIR__ . "/../currencies.json"));
    }

    /**
     * Set Currencies
     *
     * Write the currencies, along with their exchange rates to
     * the exchange.json file in the ~/storage directory.
     *
     * @param \stdClass $currencies
     * @param int|null $timestamp
     *
     * @return int
     */
    private static function _setCurrencies(\stdClass $currencies, $timestamp = null)
    {
        $file = new \stdClass();

        $file->updated = (new \DateTime)->setTimestamp($timestamp);
        $file->currencies = $currencies;

        return file_put_contents(__DIR__ . "/../storage/exchange.json", json_encode($file));
    }
}