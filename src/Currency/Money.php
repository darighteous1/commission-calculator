<?php

namespace App\Currency;

use App\Exception\BaseException;
use Maba\Component\Monetary\Exception\InvalidAmountException;

class Money
{
    /**
     * @var float
     */
    private $amount;

    /**
     * @var Currency
     */
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

    /**
     * @param float $amount
     *
     * @return Money
     * @throws InvalidAmountException
     */
    private function setAmount(float $amount)
    {
        if ($amount < 0) {
            throw new InvalidAmountException(BaseException::EXIT_INVALID_MONEY_AMOUNT);
        }

        $this->amount = $amount;

        return $this;
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
     *
     * @return Money
     */
    private function setCurrency(Currency $currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
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
     * @param Money $money
     *
     * @return bool
     */
    public function equals(self $money)
    {
        return $this->getCurrency()->getCurrency() === $money->getCurrency()->getCurrency()
            && $this->getAmount() === $money->getAmount();
    }

    /**
     * @param Currency
     *
     * @return bool
     */
    public function isCurrency(Currency $currency): bool
    {
        return $this->getCurrency()->getCurrency() === $currency->getCurrency();
    }

    /**
     * @param Money $money
     *
     * @return Money
     */
    public function add(self $money): self
    {
        $amount = $this->getAmount() + $money->convertTo($this->getCurrency())->getAmount();

        return new self($amount, $this->getCurrency());
    }

    /**
     * Subtracts the amount of the passed Money object
     * from the amount of the current object.
     *
     * @param Money
     *
     * @return Money
     */
    public function subtract(self $money): self
    {
        $a = $this->getAmount();
        $b = $money->convertTo($this->getCurrency())->getAmount();

        // This should probably throw an exception if $b is greater than $a
        $amount = $a - $b;

        return new self(abs($amount), $this->getCurrency());
    }
}
