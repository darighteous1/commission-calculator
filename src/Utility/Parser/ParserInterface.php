<?php

namespace App\Utility\Parser;

interface ParserInterface
{
    /**
     * @param string $fileName
     *
     * @return ParserInterface
     */
    public function setFilename(string $fileName);

    /**
     * Parses next transaction data
     */
    public function parse();
}
