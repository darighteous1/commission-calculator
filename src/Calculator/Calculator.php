<?php

namespace App\Calculator;

use App\Currency\Currency;
use App\Currency\Money;
use App\Exception\BaseException;
use App\Transaction\Transaction;
use App\Transaction\Exception\InvalidTransactionTypeException;
use App\Transaction\TransactionRow;
use App\User\User;
use App\Utility\IOHandler\InputHandler;
use App\Utility\IOHandler\OutputHandler;

class Calculator
{
    const DEFAULT_CASH_OUT_COMMISSION = 0.003;
    const DEFAULT_CASH_IN_COMMISSION = 0.0003;
    const MAX_CASH_IN_COMMISSION_EUR = 5;
    const MIN_COMMISSION_LEGAL_ENTITIES_EUR = 0.5;
    const FREE_TRANSACTIONS_PER_WEEK = 3;
    const FREE_TRANSACTION_LIMIT_EUR = 1000;

    /**
     * @var User[]
     */
    private $processedUsers;
    private $inputHandler;
    private $outputHandler;

    /**
     * @var User
     */
    private $currentUser;

    public function __construct(InputHandler $inputHandler, OutputHandler $outputHandler)
    {
        $this->processedUsers = [];
        $this->inputHandler = $inputHandler;
        $this->outputHandler = $outputHandler;
    }

    public function calculate()
    {
        /** @var TransactionRow $transactionRow */
        foreach ($this->inputHandler->getNextTransactionRow() as $transactionRow) {
            $this->currentUser = $this->getCurrentUser($transactionRow);
            $transaction = new Transaction($transactionRow);

            $this->currentUser->addUserTransaction($transaction);
            $commission = $this->calculateCommission($transaction);

            $output = sprintf(
                "%.{$commission->getCurrency()->getFraction()}f",
                $this->roundCommission($commission)
            );

            $this->outputHandler->logToConsole($output);
        }
    }

    /**
     * @param Transaction $transaction
     *
     * @return Money
     */
    public function calculateCommission(Transaction $transaction): Money
    {
        if ($transaction->getTransactionType() === Transaction::TYPE_CASH_IN) {
            return $this->calculateCommissionCashIn($transaction);
        } elseif ($transaction->getTransactionType() === Transaction::TYPE_CASH_OUT) {
            return $this->calculateCommissionCashOut($transaction);
        }

        throw new InvalidTransactionTypeException(BaseException::EXIT_INVALID_OPERATION_TYPE);
    }

    /**
     * @param Transaction $transaction
     *
     * @return Money
     */
    private function calculateCommissionCashIn(Transaction $transaction): Money
    {
        $commissionAmount = $transaction->getTransactionAmount() * self::DEFAULT_CASH_IN_COMMISSION;
        $commission = new Money($commissionAmount, $transaction->getTransactionCurrency());
        $commissionEUR = $commission->convertTo(new Currency(Currency::EUR));

        if ($commissionEUR->getAmount() > self::MAX_CASH_IN_COMMISSION_EUR) {
            $commission = new Money(self::MAX_CASH_IN_COMMISSION_EUR, new Currency(Currency::EUR));
            $commission = $commission->convertTo($transaction->getTransactionCurrency());
        }

        return $commission;
    }

    /**
     * @param Transaction $transaction
     *
     * @return Money
     */
    private function calculateCommissionCashOut(Transaction $transaction): Money
    {
        if ($this->currentUser->getUserType() === User::USER_TYPE_LEGAL) {
            $commissionAmount = $transaction->getTransactionAmount() * self::DEFAULT_CASH_OUT_COMMISSION;
            $maxCommission = new Money(self::MAX_CASH_IN_COMMISSION_EUR, new Currency(Currency::EUR));

            if ($transaction->getTransactionCurrency()->getCurrency() === Currency::EUR) {
                $commissionAmount = min($commissionAmount, self::MAX_CASH_IN_COMMISSION_EUR);
            } else {
                $commissionAmount = min(
                    $commissionAmount,
                    $maxCommission->convertTo($transaction->getTransactionCurrency())
                        ->getAmount()
                );
            }

            return new Money($commissionAmount, $transaction->getTransactionCurrency());
        }

        $baseAmount = $transaction->calculateCommissionBaseAmount($this->currentUser);

        return new Money(
            $baseAmount->getAmount() * self::DEFAULT_CASH_OUT_COMMISSION,
            $transaction->getTransactionCurrency()
        );
    }

    /**
     * @param TransactionRow $row
     *
     * @return User
     */
    private function getCurrentUser(TransactionRow $row): User
    {
        if (!isset($this->processedUsers[$row->getUserId()])) {
            $currentUser = new User($row->getUserId(), $row->getUserType());
            $this->processedUsers[$currentUser->getUserId()] = $currentUser;
        } else {
            /** @var User $currentUser */
            $currentUser = $this->processedUsers[$row->getUserId()];
        }

        return $currentUser;
    }

    /**
     * @param Money $commission
     *
     * @return float
     */
    private function roundCommission(Money $commission): float
    {
        return ceil($commission->getAmount() * 100) / 100;
    }
}
