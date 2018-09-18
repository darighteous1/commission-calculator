<?php

namespace App\Currency;

class ConversionRateChecker
{
    const CONVERSION_RATES = [
        Currency::EUR => [
            Currency::USD => 1.1497,
            Currency::JPY => 129.53,
        ],
    ];

    /**
     * Conversion rate is the same for A -> B and B -> A
     * But we'll raise the rate to the power of -1 when converting to EUR.
     *
     * @see CurrencyConverter::convertCurrency()
     *
     * @param string $convertFrom
     * @param string $convertTo
     *
     * @return float
     */
    public function getConversionRate(string $convertFrom, string $convertTo): float
    {
        // Here, we'd usually make an api call to get the conversion rate
        return self::CONVERSION_RATES[$convertFrom][$convertTo] ?? self::CONVERSION_RATES[$convertTo][$convertFrom];
    }

    /**
     * Returns 1 if converting FROM EUR and -1 otherwise.
     *
     * @see CurrencyConverter::convertCurrency()
     *
     * @param string $convertFrom
     *
     * @return int
     */
    public function getConversionAction(string $convertFrom): int
    {
        return $convertFrom === Currency::EUR ? 1 : -1;
    }
}
