<?php

namespace App\Utility\Mapper;

use App\Transaction\TransactionRow;

class CsvMapper implements DenormalizerInterface
{
    /**
     * @param array $data
     * @return TransactionRow
     */
    public function mapToEntity($data): TransactionRow
    {
        $transactionRow = new TransactionRow(
            array_pop($data), // row number
            $data[0], // date
            $data[1], // user id
            $data[2], // user type
            $data[3], // operation type
            $data[4], // amount
            $data[5] // currency
        );

        return $transactionRow;
    }
}
