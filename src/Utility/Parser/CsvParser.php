<?php

namespace App\Utility\Parser;

use App\Exception\InvalidRowException;
use App\Transaction\TransactionRow;

class CsvParser implements ParserInterface
{

    private $fileHandle;

    /**
     * Sets the file name
     *
     * @param string $fileName
     * @return void
     */
    public function setFile(string $fileName)
    {
        $this->fileHandle = fopen($fileName, 'r');
    }

    /**
     * Reads the next transaction data from a file
     * yields zero-based array
     */
    public function parse()
    {
        $row = 0;
        while (!feof($this->fileHandle)) {
            $data = fgetcsv($this->fileHandle);
            ++$row;

            if (count($data) !== TransactionRow::EXPECTED_NUMBER_OF_COLUMNS) {
                throw InvalidRowException::incorrectColumns($row, count($data));
            }

            $data[] = $row;

            yield $data;
        }

        fclose($this->fileHandle);
    }
}