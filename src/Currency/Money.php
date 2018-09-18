<?php

namespace App\Currency;

use App\Exception\MoneyException;

class Money
{
    /**
     * @var float
     */
    private $amount;
    private $currency;

    public function __construct(float $amount, Currency $currency)
    {
        $this->setAmount($amount);
        $this->setCurrency($currency);
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    private function setAmount(float $amount)
    {
        if ($amount < 0) {
            throw MoneyException::invalidAmount($amount);
        }

        $this->amount = $amount;
    }

    /**
     * @return Currency
     */
    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * @param Currency $currency
     */
    private function setCurrency(Currency $currency)
    {
        $this->currency = $currency;
    }

    /**
     * Converts the amount to the given Currency.
     *
     * @param Currency $currency
     *
     * @return Money
     */
    public function convertTo(Currency $currency)
    {
        $currencyConverter = new CurrencyConverter(new ConversionRateChecker()); // FIXME: this is bad
        $convertedAmount = $currencyConverter->convertCurrency($this->amount, $this->currency, $currency);

        return new self($convertedAmount, $currency);
    }

    /**
     * Checks if two Money objects are equal.
     *
     * @param Money $money
     *
     * @return bool
     */
    public function equals(Money $money)
    {
        return $this->getCurrency()->getCode() === $money->getCurrency()->getCode()
            && $this->getAmount() === $money->getAmount();
    }

    /**
     * Returns true if the Currency of this Money object
     * is the same as the passed argument.
     *
     * @param Currency $currency
     *
     * @return bool
     */
    public function isCurrency(Currency $currency): bool
    {
        return $this->getCurrency()->getCode() === $currency->getCode();
    }

    /**
     * Adds the amount of the passed Money object
     * to the amount of the current object.
     *
     * @param Money $money
     *
     * @return Money
     */
    public function add(Money $money): Money
    {
        $amount = $this->getAmount() + $money->convertTo($this->getCurrency())->getAmount();

        return new self($amount, $this->getCurrency());
    }

    /**
     * Subtracts the amount of the passed Money object
     * from the amount of the current object.
     *
     * @param Money $money
     *
     * @return Money
     */
    public function subtract(Money $money): Money
    {
        $a = $this->getAmount();
        $b = $money->convertTo($this->getCurrency())->getAmount();

        // This should probably throw an exception if $b is greater than $a
        $amount = $a - $b;

        return new self(abs($amount), $this->getCurrency());
    }
}
