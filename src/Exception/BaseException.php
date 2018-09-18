<?php

namespace App\Exception;


use Throwable;

class BaseException extends \Exception
{
    const EXIT_INVALID_CURRENCY = 1;
    const EXIT_INVALID_FILE_FORMAT = 2;
    const EXIT_INVALID_CLIENT_TYPE = 3;
    const EXIT_INVALID_OPERATION_TYPE = 4;
    const EXIT_INVALID_DATE_FORMAT = 5;
    const EXIT_INVALID_OPERATION_AMOUNT = 6;

    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}