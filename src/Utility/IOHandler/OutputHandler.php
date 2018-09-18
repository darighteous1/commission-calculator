<?php

namespace App\Utility\IOHandler;

class OutputHandler
{
    const LOGS_DIR = __DIR__.'/../../../logs/';

    /**
     * Prints to the STDOUT.
     *
     * @param string $output
     */
    public function print($output)
    {
        fwrite(STDOUT, $output.PHP_EOL);
    }

    public function log(string $filename, $data)
    {
        file_put_contents(self::LOGS_DIR.$filename, print_r($data, true), FILE_APPEND);
    }
}
