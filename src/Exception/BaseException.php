<?php

namespace App\Exception;

use Exception;
use Throwable;

class BaseException extends Exception
{
    const EXIT_INVALID_CURRENCY = 1;
    const EXIT_INVALID_FILE_FORMAT = 2;
    const EXIT_INVALID_CLIENT_TYPE = 3;
    const EXIT_INVALID_OPERATION_TYPE = 4;
    const EXIT_INVALID_DATE_FORMAT = 5;
    const EXIT_INVALID_OPERATION_AMOUNT = 6;
    const EXIT_INVALID_MONEY_AMOUNT = 7;
    const EXIT_INVALID_CSV_ROW = 8;

    const MESSAGES = [
        self::EXIT_INVALID_CURRENCY => 'Invalid currency.',
        self::EXIT_INVALID_FILE_FORMAT => 'Invalid file format.',
        self::EXIT_INVALID_CLIENT_TYPE => 'Invalid client type.',
        self::EXIT_INVALID_OPERATION_TYPE => 'Invalid operation type.',
        self::EXIT_INVALID_DATE_FORMAT => 'Invalid date format.',
        self::EXIT_INVALID_OPERATION_AMOUNT => 'Invalid operation amount',
        self::EXIT_INVALID_CSV_ROW => 'Invalid csv row.',
    ];

    public function __construct(int $code = 0, Throwable $previous = null)
    {
        $message = self::MESSAGES[$code];
        parent::__construct($message, $code, $previous);
    }
}
