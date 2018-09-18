<?php

namespace App\Utility\Mapper;


use App\Transaction\TransactionRow;

interface DenormalizerInterface
{
    /**
     * Maps array to entity
     * @param array|object $data
     * @return TransactionRow
     */
    public function mapToEntity($data): TransactionRow;
}