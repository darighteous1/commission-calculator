<?php

namespace App\Exception;


class CurrencyException extends BaseException
{
    public static function invalidCurrencyCode($code)
    {
        return new self(
            sprintf('%s is not a valid currency code.', $code),
            self::EXIT_INVALID_CURRENCY
        );
    }
}