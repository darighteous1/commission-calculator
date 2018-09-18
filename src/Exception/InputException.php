<?php
/**
 * Created by PhpStorm.
 * User: darighteous1
 * Date: 11/09/18
 * Time: 11:30
 */

namespace App\Exception;


class InputException extends BaseException
{
    public static function invalidFileFormat($format)
    {
        return new static(
            sprintf("%s is not a valid file format.", $format),
            self::EXIT_INVALID_FILE_FORMAT
        );
    }
}