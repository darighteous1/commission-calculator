<?php

use App\Exception\BaseException;
use App\Exception\ExceptionHandler;
use App\Utility\IOHandler\InputHandler;
use App\Utility\IOHandler\OutputHandler;
use App\Utility\Mapper\CsvMapper;
use App\Utility\Mapper\JsonMapper;
use App\Utility\Parser\CsvParser;
use App\Utility\Parser\JsonParser;

$outputHandler = new OutputHandler();
$exceptionHandler = new ExceptionHandler($outputHandler);

try {
    if (defined('STDIN')) {
        if (strtolower($argv[2]) === 'json') {
            $parser = new JsonParser();
            $mapper = new JsonMapper();
        } else {
            $parser = new CsvParser();
            $mapper = new CsvMapper();
        }

        $parser->setFilename($argv[1]);

        $inputHandler = new InputHandler($parser, $mapper);

        $calculator = new App\Calculator\Calculator($inputHandler, $outputHandler);
        $calculator->calculate();
    }
} catch (BaseException $exception) {
    $exceptionHandler->handleException($exception);
}
