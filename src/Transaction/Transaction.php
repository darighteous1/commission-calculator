<?php

namespace App\Transaction;

use DateTime;
use InvalidArgumentException;
use App\Exception\BaseException;
use App\Calculator\Calculator;
use App\User\User;
use App\Currency\Money;
use App\Currency\Currency;
use App\Transaction\Exception\InvalidTransactionTypeException;

class Transaction
{
    const TYPE_CASH_IN = 'cash_in';
    const TYPE_CASH_OUT = 'cash_out';

    const ALLOWED_OPERATIONS = [
        self::TYPE_CASH_IN,
        self::TYPE_CASH_OUT,
    ];

    /**
     * @var Money
     */
    private $money;

    /**
     * @var string
     */
    private $transactionType;

    /**
     * @var DateTime
     */
    private $transactionDate;

    /**
     * It consists of the week number and year number of the transaction
     * to avoid miscalculating commission fees for transactions that are in the same week, but in different year
     *
     * @var string
     */
    private $transactionWeek;

    public function __construct(TransactionRow $row)
    {
        $this->setTransactionType($row->getOperationType());
        $this->setTransactionDate($row->getDate());
        $this->money = new Money(
            $row->getAmount(),
            new Currency($row->getCurrency())
        );

        $this->transactionWeek = (function ($transactionDate) {
            /** @var DateTime */
            return $transactionDate->format('W') . $transactionDate->format('Y');
        })(
            $this->getTransactionDate()
        );
    }

    /**
     * @param string $transactionType
     *
     * @return Transaction
     * @throws InvalidTransactionTypeException
     */
    private function setTransactionType(string $transactionType)
    {
        if (!in_array($transactionType, self::ALLOWED_OPERATIONS, true)) {
            throw new InvalidTransactionTypeException(BaseException::EXIT_INVALID_OPERATION_TYPE);
        }

        $this->transactionType = $transactionType;

        return $this;
    }

    /**
     * @param string $transactionDate
     *
     * @return Transaction
     * @throws InvalidArgumentException
     */
    private function setTransactionDate(string $transactionDate)
    {
        if (!preg_match("(\d{4}-\d{2}-\d{2})", $transactionDate)) {
            throw new InvalidArgumentException('Wrong date format.');
        }

        $this->transactionDate = DateTime::createFromFormat('Y-d-m', $transactionDate);

        return $this;
    }

    /**
     * @return string
     */
    public function getTransactionType(): string
    {
        return $this->transactionType;
    }

    /**
     * @return float
     */
    public function getTransactionAmount(): float
    {
        return $this->money->getAmount();
    }

    /**
     * @return Money
     */
    public function getMoney(): Money
    {
        return $this->money;
    }

    /**
     * @return DateTime
     */
    public function getTransactionDate(): DateTime
    {
        return $this->transactionDate;
    }

    /**
     * @return Currency
     */
    public function getTransactionCurrency(): Currency
    {
        return $this->money->getCurrency();
    }

    /**
     * @return string
     */
    public function getTransactionWeek(): string
    {
        return $this->transactionWeek;
    }

    /**
     * NOTE: currently not used.
     *
     * @param User $user
     *
     * @return bool
     */
    public function isFreeOfCommission(User $user): bool
    {
        return !$this->isFullCommission($user)
            && !$this->isPartialCommission($user);
    }

    /**
     * @param User $user
     *
     * @return Money
     */
    public function calculateCommissionBaseAmount(User $user): Money
    {
        if ($this->isFullCommission($user)) {
            return new Money($this->getTransactionAmount(), $this->getTransactionCurrency());
        }

        if ($this->isPartialCommission($user)) {
            $capSpace = abs($user->getTransactionsData()[$this->getTransactionWeek()]['weeklyTransactionsCapSpace']);
            $amount = new Money($capSpace, new Currency(Currency::EUR));
            $amount = $amount->convertTo($this->getTransactionCurrency());

            $user->weeklyFreeTransactionsLimitReached()[$this->getTransactionWeek()] = true;

            return $amount;
        }

        return new Money(0, $this->getTransactionCurrency());
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    private function isFullCommission(User $user): bool
    {
        $transactionCount = $user->getTransactionsData()[$this->getTransactionWeek()]['weeklyTransactionsCount'];

        return $this->getTransactionType() === self::TYPE_CASH_IN
            || $user->getUserType() === User::USER_TYPE_LEGAL
            || $transactionCount > Calculator::FREE_TRANSACTIONS_PER_WEEK
            || isset($user->weeklyFreeTransactionsLimitReached()[$this->getTransactionWeek()]);
    }

    /**
     * Cap-space is calculated when we subtract the cash-out Transaction total for the current week
     * from the free limit (@see Calculator::FREE_TRANSACTION_LIMIT_EUR)
     *
     * @param User $user
     *
     * @return bool
     */
    private function isPartialCommission(User $user): bool
    {
        return $user->getTransactionsData()[$this->getTransactionWeek()]['weeklyTransactionsCapSpace'] < 0;
    }
}
