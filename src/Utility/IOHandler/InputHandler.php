<?php

namespace App\Utility\IOHandler;

use App\Utility\Mapper\DenormalizerInterface;
use App\Utility\Parser\ParserInterface;

class InputHandler
{
    const EXPECTED_NUMBER_OF_COLUMNS = 6;

    /**
     * @var ParserInterface
     */
    private $parser;
    /**
     * @var DenormalizerInterface
     */
    private $mapper;

    public function __construct(ParserInterface $parser, DenormalizerInterface $mapper)
    {
        $this->parser = $parser;
        $this->mapper = $mapper;
    }

    /**
     * yields TransactionRow
     */
    public function getNextTransactionRow()
    {
        foreach ($this->parser->parse() as $data) {
            $transactionRow = $this->mapper->mapToEntity($data);
            yield $transactionRow;
        }
    }
}
