# Abacus

[![Build Status](https://travis-ci.org/elliotwms/Abacus.svg?branch=master)](https://travis-ci.org/elliotwms/Abacus)
[![Latest Stable Version](https://poser.pugx.org/elliotwms/abacus/v/stable.svg)](https://packagist.org/packages/elliotwms/abacus)
[![Latest Unstable Version](https://poser.pugx.org/elliotwms/abacus/v/unstable.svg)](https://packagist.org/packages/elliotwms/abacus)
[![Total Downloads](https://poser.pugx.org/elliotwms/abacus/downloads.svg)](https://packagist.org/packages/elliotwms/abacus)
[![License](https://poser.pugx.org/elliotwms/abacus/license.svg)](https://packagist.org/packages/elliotwms/abacus)
[![Code Climate](https://codeclimate.com/github/elliotwms/Abacus/badges/gpa.svg)](https://codeclimate.com/github/elliotwms/Abacus)
[![Test Coverage](https://codeclimate.com/github/elliotwms/Abacus/badges/coverage.svg)](https://codeclimate.com/github/elliotwms/Abacus)

PHP currency manipulation package from the future. Still in very early development.

## Usage

Once Abacus has been [set up successfully](#installation), it can be used like so:

```PHP
$abacus = new Abacus(1250.00);          // Create a new Abacus object. Defaults to USD
echo $abacus;                           // "1250.00"
echo $abacus->format();                 // "$1,250.00"
echo $abacus->value                     // 1250

$abacus->to("GBP");                     // Convert USD to GBP

$abacus->add(20);                       // Addition
$abacus->add(10, "GBP");                // Addition of a value in another currency
$abacus->add(new Abacus(5, "GBP");      // Adding another Abacus object
                                        
$abacus->sub(20);                       // Subtraction
$abacus->sub(10, "GBP");                // Subtraction of a value in another currency
$abacus->sub(new Abacus(5, "GBP");      // Subtract another Abacus object
```

## Installation

Install Abacus via [Composer](//getcomposer.org) by either including it in your composer.json
file:

```JSON
{
    "require": {
        "elliotwms/abacus": "dev-master"
    }
}
```

Or by running:

```Shell
composer require elliotwms/abacus dev-master
```

### Polling the API

Abacus depends on data retrieved from the [Open Exchange Rates](https://openexchangerates.org/)
API. In order to use Abacus fully, you must poll the API using your own API key. Abacus will look
for an environment variable named `ABACUS_OPEN_EXCHANGE_KEY` and can be polled in several ways.

I recommend setting up a CRON service to poll the Open Exchange hourly in order to keep an up to
date record of the currency exchange rates. At the time of writing, the free tier of the Open
Exchange Rates API allows for 1,000 calls per month and there are 744 hours in a month so you're
sorted. It would be fruitless to poll more than once an hour on the free tier as the information
is updated hourly.

If you want more up-to-the-minute exchange rates, I highly recommend
[signing up for a paid plan](//openexchangerates.org/signup)

#### Using the included script

Abacus includes a shell script to minimize the setup process.

You can call it like so (with absolute filepaths for your CRON service) from the root of your
project directory:

    php vendor/bin/abacus

By default it will use the `ABACUS_OPEN_EXCHANGE_KEY` environment variable, but if you need to
specify it manually you can pass it as an argument:

    php vendor/bin/abacus my_super_secret_api_key

#### Doing it manually

```PHP
Currency::update();
```

Abacus will also accept an API key directly:

```PHP
Currency::update('my_api_key');
```
