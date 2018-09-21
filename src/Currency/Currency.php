<?php

namespace App\Currency;

use App\Currency\Exception\InvalidCurrencyException;
use App\Exception\BaseException;
use InvalidArgumentException;

class Currency
{
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
     */
    private $currency;

    /**
     * @var int fraction
     */
    private $fraction;

    public function __construct(string $currencyCode)
    {
        $this->setCurrency($currencyCode);
        $this->setFraction($currencyCode);
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @param string $currencyCode
     *
     * @return Currency
     * @throws InvalidCurrencyException
     */
    private function setCurrency(string $currencyCode)
    {
        if (!in_array($currencyCode, self::CURRENCY_MAP, true)) {
            throw new InvalidCurrencyException(BaseException::EXIT_INVALID_CURRENCY);
        }

        $this->currency = $currencyCode;

        return $this;
    }

    /**
     * @return int
     */
    public function getFraction(): int
    {
        return $this->fraction;
    }

    /**
     * @param string $currencyCode
     *
     * @return Currency
     * @throws InvalidArgumentException
     */
    private function setFraction(string $currencyCode)
    {
        if (self::FRACTION_MAP[$currencyCode] < 0) {
            throw new InvalidArgumentException(sprintf('Invalid fraction for Currency %s.', $currencyCode));
        }

        $this->fraction = self::FRACTION_MAP[$currencyCode];

        return $this;
    }
}
