<?php

namespace App\Utility\Parser;

class JsonParser implements ParserInterface
{
    private $fileName;

    /**
     * Sets the file name
     *
     * @param string $fileName
     * @return void
     */
    public function setFile(string $fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * Parses transaction data for one transaction at a time from json file
     * yields assoc array
     */
    public function parse()
    {
        $data = file_get_contents($this->fileName);
        $parsedData = json_decode($data);

        $row = 0;
        foreach ($parsedData as $transactionData) {
            $transactionData->row = ++$row;

            yield $transactionData;
        }
    }
}