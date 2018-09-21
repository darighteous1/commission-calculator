<?php

namespace App\Utility\Parser;

class JsonParser implements ParserInterface
{
    /**
     * @var string
     */
    private $fileName;

    /**
     * @param string $fileName
     *
     * @return JsonParser
     */
    public function setFilename(string $fileName)
    {
        $this->fileName = $fileName;

        return $this;
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
