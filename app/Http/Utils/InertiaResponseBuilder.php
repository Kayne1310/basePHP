<?php

namespace App\Http\Utils;

use Inertia\Inertia;

class InertiaResponseBuilder
{
    const FAIL    = 0;
    const SUCCESS = 1;

    const TYPE_INFO = 'info';
    const TYPE_SUCCESS = 'success';
    const TYPE_ERROR = 'error';
    const TYPE_WARNING = 'warning';

    /**
     * Success
     *
     * @param  string $path
     * @param  mixed $data
     * @param  string $message
     * @param  integer $code
     * @param  string $type [success, error, warning, info]
     * @return mixed
     */
    public static function Success($path = null, $data = null, $message = 'OK', $code = 200, $type = 'info')
    {
        if ($path == null) {
            return redirect()->back()->with($type, $message);
        } else {
            $res = [
                'status'  => self::SUCCESS,
                'code'    => $code,
                'message' => $message,
                'data'    => $data,
                $type   => $message
            ];
            return Inertia::render($path, $res);
        }
    }

    /**
     * Fail
     *
     * @param  string $message
     * @param  integer $errorCode
     * @return mixed
     */
    public static function Fail($message = 'Có lỗi xảy ra', $errorCode = 400)
    {
        return redirect()->back()->with('error', $message);
    }
}
