<?php

namespace App\User;

use App\Exception\BaseException;
use App\User\Exception\InvalidUserTypeException;
use InvalidArgumentException;
use App\Calculator\Calculator;
use App\Currency\Currency;
use App\Transaction\Transaction;

class User
{
    const USER_TYPE_NATURAL = 'natural';
    const USER_TYPE_LEGAL = 'legal';

    const USER_TYPES_MAP = [
        self::USER_TYPE_NATURAL,
        self::USER_TYPE_LEGAL,
    ];

    /**
     * Holds information about the number of Transaction for this user, split by weeks
     * as well as the remaining Transaction amount before owing a commission fee, also split by weeks
     *
     * Each element of the array has a key that is the week number + the year of the transaction (e.g. 012016)
     * This is done to avoid miscalculations if we need to calculate commissions over large periods of time
     *
     * Each value is an associative array itself, with keys weeklyTransactionsCount and weeklyTransactionsCapSpace
     * weeklyTransactionsCount holds the number of performed Transaction for the specified week
     * weeklyTransactionsCapSpace holds the remaining amount in EUR before reaching the specified limit
     * of free transaction amount (@see Calculator::FREE_TRANSACTION_LIMIT_EUR)
     *
     * @var array
     */
    private $transactionsData;

    /**
     * The first time the weekly cash-out limit is reached
     * for a given week, this will be set to true,
     * so any consequent cash-out Transaction in the same week
     * will be subject to full commission.
     *
     * @var array
     */
    private $weeklyFreeTransactionsLimitReached;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var string
     */
    private $userType;

    public function __construct(int $userId, string $userType)
    {
        $this->setUserId($userId);
        $this->setUserType($userType);
        $this->transactionsData = [];
        $this->weeklyFreeTransactionsLimitReached = [];
    }

    /**
     * @return array
     */
    public function weeklyFreeTransactionsLimitReached(): array
    {
        return $this->weeklyFreeTransactionsLimitReached;
    }

    /**
     * @return string
     */
    public function getUserType(): string
    {
        return $this->userType;
    }

    /**
     * @param string $type
     *
     * @return User
     * @throws InvalidUserTypeException
     */
    public function setUserType(string $type)
    {
        $userType = trim($type);

        if (!in_array($userType, self::USER_TYPES_MAP, true)) {
            throw new InvalidUserTypeException(BaseException::EXIT_INVALID_CLIENT_TYPE);
        }

        $this->userType = $userType;

        return $this;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $id
     *
     * @return User
     * @throws InvalidArgumentException
     */
    public function setUserId(int $id)
    {
        if ($id <= 0) {
            throw new InvalidArgumentException(sprintf('%s is not a valid id.', $id));
        }

        $this->userId = $id;

        return $this;
    }

    /**
     * @return array
     */
    public function getTransactionsData(): array
    {
        return $this->transactionsData;
    }

    public function addUserTransaction(Transaction $transaction)
    {
        if ($transaction->getTransactionType() === Transaction::TYPE_CASH_IN
            || $this->getUserType() === self::USER_TYPE_LEGAL) {
            return;
        }

        $week = $transaction->getTransactionWeek();
        $amountInEUR = $transaction->getMoney()->convertTo(new Currency(Currency::EUR));

        if (!isset($this->transactionsData[$week])) {
            $capSpace = Calculator::FREE_TRANSACTION_LIMIT_EUR - $amountInEUR->getAmount();
            $this->transactionsData[$week]['weeklyTransactionsCount'] = 1;
            $this->transactionsData[$week]['weeklyTransactionsCapSpace'] = $capSpace;
        } else {
            $capSpace = $this->transactionsData[$week]['weeklyTransactionsCapSpace'] - $amountInEUR->getAmount();
            ++$this->transactionsData[$week]['weeklyTransactionsCount'];
            $this->transactionsData[$week]['weeklyTransactionsCapSpace'] = $capSpace;
        }
    }

    /**
     * @param string $week
     *
     * @return bool
     */
    public function isFreeTransactionsNumberReached(string $week): bool
    {
        return $this->transactionsData[$week]['weeklyTransactionsCount'] > Calculator::FREE_TRANSACTIONS_PER_WEEK;
    }

    /**
     * @param string $week
     *
     * @return bool
     */
    public function isWeeklyCapSpaceReached(string $week): bool
    {
        return $this->transactionsData[$week]['weeklyTransactionsCapSpace'] <= 0;
    }
}
