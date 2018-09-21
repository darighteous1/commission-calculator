<?php

namespace App\Exception;

use App\Utility\IOHandler\OutputHandler;

class ExceptionHandler
{
    const LOG_FILE_NAME = 'exceptions_log';
    private $outputHandler;

    public function __construct(OutputHandler $outputHandler)
    {
        $this->outputHandler = $outputHandler;
    }

    public function handleException(BaseException $exception)
    {
        $this->outputHandler->log(
            self::LOG_FILE_NAME,
            $exception->getTraceAsString() . PHP_EOL . PHP_EOL
        );

        if ($exception->getPrevious() !== null) {
            $this->outputHandler->log(
                self::LOG_FILE_NAME,
                $exception->getPrevious()
                    ->getTraceAsString()
            );
        }

        echo $exception->getMessage() . PHP_EOL;
        exit($exception->getCode());
    }
}
