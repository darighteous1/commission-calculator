<?php

namespace App\Utility\IOHandler;

class OutputHandler
{
    const LOGS_DIR = __DIR__ . '/../../../logs/';
    const LOGS_FILE_EXT = '.log';

    /**
     * @param string $output
     */
    public function logToConsole($output)
    {
        fwrite(STDOUT, $output . PHP_EOL);
    }

    public function log(string $fileName, string $text)
    {
        $handle = fopen(self::LOGS_DIR . $fileName . self::LOGS_FILE_EXT, 'a');
        fwrite($handle, $text);
        fclose($handle);
    }

    public function logArray(string $fileName, array $data)
    {
        $handle = fopen(self::LOGS_DIR . $fileName . self::LOGS_FILE_EXT, 'a');
        fwrite($handle, implode(PHP_EOL, $data));
        fclose($handle);
    }
}
