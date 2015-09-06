<?php

namespace Abacus;

/**
 * Class Currency
 * @package Abacus
 */
class Currency
{
    /**
     * @var string
     */
    public $name = 'US Dollar';         // Common singular currency name

    /**
     * @var string
     */
    public $name_plural = 'US Dollars'; // Common plural currency name

    /**
     * @var string
     */
    public $code = 'USD';               // ISO code

    /**
     * @var string
     */
    public $symbol = '$';               // Standard currency symbol ($/£/€)

    /**
     * @var string
     */
    public $symbol_native = '$';        // Locally used currency symbol

    /**
     * @var float
     */
    public $rate = 1;                   // The currency's rate to USD

    /**
     * @var string
     */
    public $thousands_separator = ',';  // Thousands separator for formatting

    /**
     * @var string
     */
    public $decimal_separator = '.';    // Decimal separator for formatting

    /**
     * @var int
     */
    public $decimal_digits = 2;         // How many decimal digits to display

    /**
     * @var int
     */
    public $rounding = 0;               // Currency rounding method
    public $updated_at;                 // When the currency was last updated

    /**
     * __construct
     *
     * Create a new Currency object
     *
     * @param string $currency The currency's ISO code
     *
     * @throws AbacusException
     */
    public function __construct($currency = "USD")
    {
        // Get the Currency object from the exchange.json file
        $currency = $this->_getCurrency($currency);

        // Get the attributes from the currency JSON object
        $this->_mapCurrency($currency);
    }

    /**
     * Get Currency
     *
     * Gets a currency from the exchange.json
     *
     * @param $currency
     * @return mixed
     * @throws AbacusException
     */
    private function _getCurrency($currency)
    {
        if (!file_exists(static::_getExchangePath())) {
            throw new AbacusException("Exchange rates not found. Please poll");
        }

        $exchange = json_decode(file_get_contents(static::_getExchangePath()));

        // Transform the $currency variable into an instance of
        // currency object from the ol' JSON file

        if (!isset($exchange->currencies->$currency)) {
            throw new AbacusException("Currency not found");
        }

        // Get the DateTime for when the exchange was updated last
        $this->updated_at = $exchange->updated;

        return $exchange->currencies->$currency;
    }

    private function _mapCurrency($currency)
    {
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
    }

    /**
     * Update Abacus's exchange rates
     *
     * Update the contents of abacus_exchange.json by polling the
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
     * @return OpenExchangeResponse|false
     */
    private static function _fetchAPI($key)
    {
        if (is_null($key)) {
            $key = getenv('ABACUS_OPEN_EXCHANGE_KEY');
        }

        $url = "http://openexchangerates.org/api/latest.json?app_id=$key";

        $curl = curl_init($url);
        curl_setopt_array($curl, [
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

        $result = curl_exec($curl);

        if (curl_errno($curl)) {
            return false;
        };

        // Must have a correct API key in order to function properly
        if (curl_getinfo($curl, CURLINFO_HTTP_CODE) === 401) {
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
     * the exchange.json file in the temp directory.
     *
     * @param \stdClass $currencies
     * @param int|null $timestamp
     *
     * @return int
     */
    private static function _setCurrencies(\stdClass $currencies, $timestamp = null)
    {
        $file = new \stdClass();

        $file->updated = (new \DateTime('UTC'))->setTimestamp($timestamp);
        $file->currencies = $currencies;

        return file_put_contents(static::_getExchangePath(), json_encode($file));
    }

    /**
     * Get Exchange Path
     *
     * Get the path of the abacus_exchange.json file which contains the polled exchange information
     *
     * @return string
     */
    private static function _getExchangePath()
    {
        return sys_get_temp_dir() . "/abacus_exchange.json";
    }
}