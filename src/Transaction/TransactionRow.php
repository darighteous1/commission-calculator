<?php

namespace App\Transaction;

class TransactionRow
{
    const EXPECTED_NUMBER_OF_COLUMNS = 6;

    /**
     * @var int
     */
    private $columns;

    /**
     * @var int
     */
    private $rowNumber;

    /**
     * @var string
     */
    private $date;
    /**
     * @var string
     */
    private $userId;
    /**
     * @var string
     */
    private $userType;
    /**
     * @var string
     */
    private $operationType;
    /**
     * @var string
     */
    private $amount;
    /**
     * @var string
     */
    private $currency;

    public function __construct(
        string $rowNumber,
        string $date,
        string $userId,
        string $userType,
        string $operationType,
        string $amount,
        string $currency
    ) {
        $this->rowNumber = $rowNumber;
        $this->date = $date;
        $this->userId = $userId;
        $this->userType = $userType;
        $this->operationType = $operationType;
        $this->amount = $amount;
        $this->currency = $currency;
    }

    /**
     * @return int
     */
    public function getColumns(): int
    {
        return $this->columns;
    }

    /**
     * @return int
     */
    public function getRowNumber(): int
    {
        return $this->rowNumber;
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getUserType(): string
    {
        return $this->userType;
    }

    /**
     * @return string
     */
    public function getOperationType(): string
    {
        return $this->operationType;
    }

    /**
     * @return string
     */
    public function getAmount(): string
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }


}
