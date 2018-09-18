<?php

namespace App\Utility\Mapper;


use App\Transaction\TransactionRow;

class JsonMapper implements DenormalizerInterface
{

    /**
     * Maps array to entity
     * @param $data
     * @return TransactionRow
     */
    public function mapToEntity($data): TransactionRow
    {
        $transactionRow = new TransactionRow(
            $data->row,
            $data->operation_date,
            $data->user_id,
            $data->user_type,
            $data->operation_type,
            $data->amount,
            $data->currency
        );

        return $transactionRow;
    }
}