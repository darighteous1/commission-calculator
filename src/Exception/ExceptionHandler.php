<?php

namespace App\Exception;


use App\Utility\IOHandler\OutputHandler;

class ExceptionHandler
{
    /**
     * @param BaseException $exception
     */
    public function handleException(BaseException $exception)
    {
        $outputHandler = new OutputHandler();
        $outputHandler->log(
            'exceptions_log.txt',
            $exception->getTraceAsString() . PHP_EOL . PHP_EOL
        );

        echo $exception->getMessage();
        echo $exception->getPrevious();
        exit($exception->getCode());
    }
}
