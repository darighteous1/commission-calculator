<?php

namespace App\Utility\Parser;

use App\Exception\BaseException;
use App\Transaction\TransactionRow;
use App\Utility\Parser\Exception\InvalidRowException;

class CsvParser implements ParserInterface
{
    /**
     * @var resource
     */
    private $fileHandle;

    /**
     * @param string $fileName
     *
     * @return CsvParser
     */
    public function setFilename(string $fileName)
    {
        $this->fileHandle = fopen($fileName, 'r');

        return $this;
    }

    /**
     * Reads the next transaction data from a file
     * yields zero-based array
     * @throws InvalidRowException
     */
    public function parse()
    {
        $row = 0;
        while (!feof($this->fileHandle)) {
            $data = fgetcsv($this->fileHandle);
            ++$row;

            if (count($data) !== TransactionRow::EXPECTED_NUMBER_OF_COLUMNS) {
                throw new InvalidRowException(BaseException::EXIT_INVALID_FILE_FORMAT);
            }

            $data[] = $row;

            yield $data;
        }

        fclose($this->fileHandle);
    }
}
