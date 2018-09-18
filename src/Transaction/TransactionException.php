<?php

namespace App\Transaction;


use App\Exception\BaseException;

class TransactionException extends BaseException
{
    public static function invalidTransactionType($type)
    {
        return new static(
            sprintf('%s is not a valid operation type.', $type),
            self::EXIT_INVALID_OPERATION_TYPE
        );
    }

    public static function invalidTransactionAmount($amount)
    {
        return new static(
            sprintf('%s is not a valid transaction amount.', $amount),
            self::EXIT_INVALID_OPERATION_AMOUNT
        );
    }

    public static function invalidTransactionDateFormat(string $date)
    {
        return new static(
            sprintf('Invalid date format (%s)', $date),
            self::EXIT_INVALID_DATE_FORMAT
        );
    }
}