<?php

namespace Abacus;

/**
 * Class Abacus
 * @package Abacus
 */
class Abacus
{

    /**
     * Create a new Abacus object
     *
     * @param float $value
     * @param Currency|string|null $currency
     */
    public function __construct($value = null, $currency = "USD")
    {
        // Assign the value
        $this->value = $value;

        // Assign the currency
        if (is_a($currency, "Abacus\\Currency")) {
            // Existing (or custom) Currency object
            $this->currency = $currency;
        } else {
            // New Currency object
            $this->currency = new Currency($currency);
        }

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
        return $this->currency->symbol . number_format($this->value, $this->currency->decimal_digits, $this->currency->decimal_separator, $this->currency->thousands_separator);
    }

    /**
     * Typecast to string
     *
     * Should return the value in its most basic form for humans to do with
     * as they please
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->value;
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
     * Add two currencies together.
     *
     * Either add a float to an Abacus object, or add another Abacus object
     * of any currency to the current abacus object. Either way, this results
     * in an Abacus object of the original currency
     *
     * @param Abacus|float $value Monetary value of the number added
     * @param Currency|string|null $currency Currency of the number added
     * @return $this
     */
    public function add($value, $currency = null)
    {
        if (is_a($value, "Abacus\\Abacus")) {
            // If adding an Abacus object
            // Convert the object to the current object's currency and
            // add it to the current object

            $result = $this->value + $value->to($this->currency->code)->value;

            // Return a new Abacus object
            return new Abacus($result, $this->currency);
        } else {
            // If we're dealing with a different currency
            if (isset($currency)) {
                $value = new Abacus($value, $currency);
            } else {
                $value = new Abacus($value, $this->currency);
            }
            // Call Add properly with some recursive magic
            return $this->add($value);
        }
    }

    /**
     * Convert to a currency
     *
     * Convert the Abacus object from one currency to another.
     *
     * @param Currency|string $currency
     *
     * @return Abacus $this
     */
    public function to($currency)
    {
        // If we're not using a custom Currency model
        if (!is_a($currency, "Abacus\\Currency")) {
            // Make a new Currency model of the target
            $currency = new Currency($currency);
        }

        // New currency = Current / rate * new rate
        $value = $this->value / $this->currency->rate * $currency->rate;

        // Return the new Abacus object
        return new Abacus($value, $currency);
    }

}