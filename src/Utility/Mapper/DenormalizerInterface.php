<?php

namespace App\Utility\Mapper;

use App\Transaction\TransactionRow;

interface DenormalizerInterface
{
    /**
     * @param mixed $data @TODO: we do not use multiple types
     * @return TransactionRow
     */
    public function mapToEntity($data): TransactionRow;
}
