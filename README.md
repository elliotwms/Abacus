# Abacus

[![Latest Stable Version](https://poser.pugx.org/elliotwms/abacus/v/stable.svg)](https://packagist.org/packages/elliotwms/abacus)
[![Total Downloads](https://poser.pugx.org/elliotwms/abacus/downloads.svg)](https://packagist.org/packages/elliotwms/abacus) [![Latest Unstable Version](https://poser.pugx.org/elliotwms/abacus/v/unstable.svg)](https://packagist.org/packages/elliotwms/abacus)
[![License](https://poser.pugx.org/elliotwms/abacus/license.svg)](https://packagist.org/packages/elliotwms/abacus)
[![Build Status](https://travis-ci.org/elliotwms/Abacus.svg?branch=master)](https://travis-ci.org/elliotwms/Abacus)

PHP currency manipulation package from the future. Still in very early development.

## Usage

```php
$abacus = new Abacus(1250.00, "GBP");   // Create a new Abacus object. Defaults to GBP
echo $abacus;                           // "1,250.00"
echo $abacus->format();                 // "£1,250.00"
echo $abacus->value                     // 1250

$abacus->to("USD");                     // Convert GBP to USD

$abacus->add(20);                       // Addition
$abacus->sub(20);                       // Subtraction

$abacus->add(10, "GBP");                // Addition of another currency into the
                                        // original currency
$abacus->sub(10, "GBP");                // Subtraction of another currency into
                                        // the original currency

$abacus->add(new Abacus(5, "GBP");      // Adding another Abacus object of a
                                        // different currency
$abacus->sub(new Abacus(5, "GBP");      // Subtract another Abacus object of a
                                        // different currency
```
