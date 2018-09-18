<?php

namespace App\Exception;


class MoneyException extends BaseException
{
    public static function invalidAmount($amount)
    {
        return new static(
            sprintf('Invalid transaction amount (%s)', $amount),
            self::EXIT_INVALID_OPERATION_AMOUNT
        );
    }
}