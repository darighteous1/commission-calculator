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
     * @param string $convertFrom
     * @param string $convertTo
     *
     * @return float
     */
    public function getConversionRate(string $convertFrom, string $convertTo): float
    {
        return self::CONVERSION_RATES[$convertFrom][$convertTo] ?? self::CONVERSION_RATES[$convertTo][$convertFrom];
    }

    /**
     * @param string $convertFrom
     *
     * @return int
     */
    public function getConversionAction(string $convertFrom): int
    {
        return $convertFrom === Currency::EUR ? 1 : -1;
    }
}
