<?php

namespace App\Utility\Parser;

interface ParserInterface
{
    /**
     * Sets the file name
     *
     * @param string $fileName
     * @return void
     */
    public function setFile(string $fileName);

    /**
     * Parses next transaction data
     */
    public function parse();
}