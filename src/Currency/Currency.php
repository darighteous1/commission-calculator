<?php

namespace App\Currency;

use App\Exception\CurrencyException;

class Currency
{
    // NOTE: these should not be here
    const EUR = 'EUR';
    const USD = 'USD';
    const JPY = 'JPY';

    const CURRENCY_MAP = [
        self::EUR,
        self::USD,
        self::JPY,
    ];

    const FRACTION_MAP = [
        self::EUR => 2,
        self::USD => 2,
        self::JPY => 0,
    ];

    /**
     * @var string
     *             The code of the Currency, e.g. EUR
     */
    private $code;

    /**
     * @var int fraction
     *          The decimal fraction of the Currency
     *          e.g. JPY does not have fractions
     */
    private $fraction;

    public function __construct($currencyCode)
    {
        $this->setCode($currencyCode);
        $this->setFraction($currencyCode);
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    private function setCode($currencyCode)
    {
        if (!in_array($currencyCode, self::CURRENCY_MAP)) {
            throw CurrencyException::invalidCurrencyCode($currencyCode);
        }

        $this->code = $currencyCode;
    }

    /**
     * @return int
     */
    public function getFraction(): int
    {
        return $this->fraction;
    }

    private function setFraction(string $currencyCode)
    {
        if (self::FRACTION_MAP[$currencyCode] < 0) {
            throw new \InvalidArgumentException(sprintf('Invalid fraction for Currency %s.', $currencyCode));
        }

        $this->fraction = self::FRACTION_MAP[$currencyCode];
    }
}
