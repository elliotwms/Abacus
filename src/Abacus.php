<?php

namespace Abacus;

use Abacus\Currency;
use GuzzleHttp;

/**
 * Class Abacus
 * @package Abacus
 */
class Abacus {

    /**
     * Create a new Abacus object
     *
     * @param float $value
     * @param string|null $currency
     */
    public function __construct($value = null, $currency = "GBP")
    {
        $this->value = $value;
        $this->currency = new Currency($currency);
    }

    /**
     * Typecast to string
     *
     * @return string
     */
    public function __toString()
    {
        return number_format($this->value, 2, $this->currency->decimal_separator, $this->currency->thousands_separator);
    }

    /**
     * Format Abacus object
     *
     * Add the currency symbol to the front and output formatted with decimal
     * and thousands markers
     *
     * @return string
     */
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
            $this->value += $value->to($this->currency->code)->value;
        } else {
            if (isset($currency)) {
                $value = new Abacus($value, $currency);
            } else {
                $value = new Abacus($value, $this->currency->code);
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
        $currency = new Currency($currency);

        // New currency = Current / rate * new rate
        $this->value = $this->value / $this->currency->rate * $currency->rate;

        // Update the currency of the Abacus model
        $this->currency = $currency;

        return $this;
    }

    /**
     * Update Abacus's exchange rates
     *
     * Update the contents of storage/exchange.json by polling the
     * OpenExchangeRates API, getting the currencies in currencies.json
     * and combining the two.
     *
     * @param null $key
     * @return int
     */
    public static function update($key = null)
    {
        $latest = self::_fetchAPI($key);

        $currencies = self::_getCurrencies();

        foreach ($currencies as $name => &$currency) {
            if (isset($latest['rates'][$name])) {
                $currency->rate = $latest['rates'][$name];
            } else {
                unset($currencies->$name);
            }
        }

        return self::_setCurrencies($currencies, $latest['timestamp']);
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
     * @return array|false
     */
    private static function _fetchAPI($key)
    {
        if (is_null($key)) {
            $key = getenv('ABACUS_OPEN_EXCHANGE_KEY');
        }
        $guzzle = (new GuzzleHttp\client())
            ->get('http://openexchangerates.org/api/latest.json', ['query' => ['app_id' => $key]]);

        return $guzzle->json();
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
     * @param \stdClass     $currencies
     * @param int|null      $timestamp
     *
     * @return int
     */
    private static function _setCurrencies(\stdClass $currencies, $timestamp = null)
    {
        $file = new \stdClass();

        $file->updated = (new \DateTime)->setTimestamp($timestamp);
        $file->currencies = $currencies;

        return file_put_contents(__DIR__."/../storage/exchange.json", json_encode($file));
    }

}