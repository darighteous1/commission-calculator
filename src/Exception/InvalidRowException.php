<?php

namespace App\Exception;


use App\Transaction\TransactionRow;

class InvalidRowException extends BaseException
{
    public static function incorrectColumns(int $rowNumber, int $actualColumns)
    {
        return new static(
            sprintf(
                'Invalid number of columns at row #%s. Expected: %s, actual: %s.',
                $rowNumber,
                TransactionRow::EXPECTED_NUMBER_OF_COLUMNS,
                $actualColumns
            ),
            self::EXIT_INVALID_FILE_FORMAT
        );
    }
}