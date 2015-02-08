# Abacus

[![Latest Stable Version](https://poser.pugx.org/elliotwms/abacus/v/stable.svg)](https://packagist.org/packages/elliotwms/abacus)
[![Total Downloads](https://poser.pugx.org/elliotwms/abacus/downloads.svg)](https://packagist.org/packages/elliotwms/abacus) [![Latest Unstable Version](https://poser.pugx.org/elliotwms/abacus/v/unstable.svg)](https://packagist.org/packages/elliotwms/abacus)
[![License](https://poser.pugx.org/elliotwms/abacus/license.svg)](https://packagist.org/packages/elliotwms/abacus)
[![Build Status](https://travis-ci.org/elliotwms/Abacus.svg?branch=master)](https://travis-ci.org/elliotwms/Abacus)

PHP currency manipulation package from the future. Still in very early development.

## Usage

```php
$abacus = new Abacus(1250.00);          // Create a new Abacus object. Defaults to GBP
echo $abacus;                           // "1,250.00"
echo $abacus->format();                 // "£1,250.00"
echo $abacus->value                     // 1250

$abacus = new Abacus(8.8888, 'USD');    // Create a new USD Abacus object.
echo $abacus;                           // "8.89"
echo $abacus->format()                  // "$8.89"
echo $abacus->value                     // 8.8888
```
