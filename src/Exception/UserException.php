<?php

namespace App\Exception;


class UserException extends BaseException
{
    public static function invalidUserType($userType)
    {
        return new static(
            sprintf('%s is not a valid user type.', $userType),
            self::EXIT_INVALID_CLIENT_TYPE
        );
    }
}