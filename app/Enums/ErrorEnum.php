<?php

namespace App\Enums;


/**
 * @method static self OK
 * @method static self UNKNOWN_ERROR
 *
 * @method static self EXCEPTION_HTTP_NOT_FOUND
 * @method static self EXCEPTION_HTTP_METHOD_NOT_ALLOWED
 * @method static self EXCEPTION_AUTHENTICATION
 * @method static self EXCEPTION_VALIDATION
 * @method static self EXCEPTION_THROTTLE_REQUESTS
 * @method static self EXCEPTION_ACCESS_DENIED
 */
class ErrorEnum
{
    public const OK = ['code' => 200, 'description' => 'OK'];

    public const UNKNOWN_ERROR = ['code' => 500, 'description' => 'Internal Server Error, Unknown Error'];

    public const EXCEPTION_NOT_FOUND = ['code' => 404, 'description' => 'Not Found'];
    public const EXCEPTION_FORBIDDEN = ['code' => 403, 'description' => 'FORBIDDEN'];
    public const EXCEPTION_BAD_REQUEST = ['code' => 400, 'description' => 'BAD_REQUEST, UNKNOWN ERROR'];
    public const EXCEPTION_AUTHENTICATION = ['code' => 401, 'description' => 'UNAUTHORIZED'];
    public const EXCEPTION_VALIDATION = ['code' => 400, 'description' => 'BAD REQUEST, VALIDATION ERROR'];
    public const EXCEPTION_TOO_MANY_REQUEST = ['code' => 429, 'description' => 'TOO MANY REQUESTS'];
    public const EXCEPTION_ACCESS_DENIED = ['code' => 401, 'description' => 'ACCESS DENIED, PERMISSION DENIED'];

    public function getMsg()
    {
        return '';
    }

    public function getCode()
    {
        return '';
    }
}
