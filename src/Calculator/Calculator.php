<?php

namespace App\Calculator;

use App\Currency\Currency;
use App\Currency\Money;
use App\Transaction\Transaction;
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

    /** @var array $processedUsers */
    private $processedUsers;

    /** @var InputHandler $inputHandler */
    private $inputHandler;

    /** @var OutputHandler $outputHandler */
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

    /**
     * Calculates commission for each transaction contained in the processed file
     */
    public function run()
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

            $this->outputHandler->print($output);
        }
    }

    /**
     * Calculates the commission fee for a given transaction.
     *
     * @param Transaction $transaction
     *
     * @return Money
     */
    public function calculateCommission(Transaction $transaction): Money
    {
        $methodName = str_replace('_', '', ucwords($transaction->getTransactionType(), '_'));
        $methodName = 'calculateCommission'.$methodName;

        return $this->{$methodName}($transaction);
    }

    /**
     * Calculate the commission fee for Cash In operations
     * This is the same every time.
     *
     * Called dynamically by calculateCommission
     *
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
     * Calculate the commission fee for Cash Out operations
     * This is different for natural and legal persons.
     *
     * For natural persons there is a free transaction limit in EUR per week
     * as well as free transaction number per week
     * If one of those limits is reached, clients should pay a commission fee
     *
     * For legal entities, this is a default percent with lower fee limit
     *
     * Called dynamically by calculateCommission
     *
     * @param Transaction $transaction
     *
     * @return Money
     */
    private function calculateCommissionCashOut(Transaction $transaction): Money
    {
        /**
         * Legal entity
         * Always a fixed percentage from the transaction amount
         * @see self::DEFAULT_CASH_IN_COMMISSION
         * but not less than a certain amount
         * @see self::MIN_COMMISSION_LEGAL_ENTITIES_EUR
         */
        if ($this->currentUser->getUserType() === User::USER_TYPE_LEGAL) {
            $commissionAmount = $transaction->getTransactionAmount() * self::DEFAULT_CASH_OUT_COMMISSION;
            $maxCommission = new Money(self::MAX_CASH_IN_COMMISSION_EUR, new Currency(Currency::EUR));

            if ($transaction->getTransactionCurrency()->getCode() === Currency::EUR) {
                $commissionAmount = min($commissionAmount, self::MAX_CASH_IN_COMMISSION_EUR);
            } else {
                $commissionAmount = min(
                    $commissionAmount,
                    $maxCommission->convertTo($transaction->getTransactionCurrency())
                        ->getAmount()
                );
            }

            return new Money($commissionAmount, $transaction->getTransactionCurrency());
        } else {
            /**
             * This is a natural person
             * We must check whether the transaction should be free of charge
             * and if not, calculate the commission fee.
             */
            $base = $transaction->calculateCommissionBaseAmount($this->currentUser);

            return new Money(
                $base->getAmount() * self::DEFAULT_CASH_OUT_COMMISSION,
                $transaction->getTransactionCurrency()
            );
        }
    }

    /**
     * Creates a new user or
     * returns the user by ID if it already exists.
     *
     * @param TransactionRow $row
     * @return User
     */
    private function getCurrentUser(TransactionRow $row): User
    {
        /**
         * Create the user if we haven't processed it yet
         * If the user is processed, however, get it from the list of processed Users.
         */
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
     * Rounds up the commission fee to the smallest Currency fraction
     * e.g. 0.023 EUR becomes 0.03 EUR.
     *
     * @param Money $commission
     *
     * @return float
     */
    private function roundCommission(Money $commission): float
    {
        return ceil($commission->getAmount() * 100) / 100;
    }
}
