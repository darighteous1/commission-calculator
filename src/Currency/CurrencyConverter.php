<?php

namespace App\Currency;

class CurrencyConverter
{
    /**
     * @var ConversionRateChecker
     */
    private $conversionRateChecker;

    public function __construct(ConversionRateChecker $conversionRateChecker)
    {
        $this->conversionRateChecker = $conversionRateChecker;
    }

    /**
     * Converts one Currency to another
     * Currently, only supports direct conversion from and to EUR
     * If else is needed, we can do double conversion
     * e.g. from USD to JPY, we can convert USD->EUR and then EUR->JPY.
     *
     * @param float    $amount
     * @param Currency $convertFrom
     * @param Currency $convertTo
     *
     * @return float
     */
    public function convertCurrency(float $amount, Currency $convertFrom, Currency $convertTo): float
    {
        if ($convertFrom->getCode() === $convertTo->getCode()) {
            return $amount;
        }

        $currencyCode = $convertFrom->getCode();
        $conversionRate = $this->conversionRateChecker->getConversionRate($currencyCode, $convertTo->getCode());

        return $amount * pow($conversionRate, $this->conversionRateChecker->getConversionAction($currencyCode));
    }
}
