<?php

namespace App\Http\Utils;

use App\Exceptions\ApiException;

class ResponseBuilder
{
    const FAIL    = 0;
    const SUCCESS = 1;

    public static function Success($data = null, $message = '', $code = 200)
    {
        $res = [
            'status'  => self::SUCCESS,
            'code'    => $code,
            'message' => $message,
            'data'    => $data,
        ];
        return response()->json($res);
    }

    public static function Fail($message = '', $data = null, $errorCode = 400)
    {
        $res = [
            'status'  => self::FAIL,
            'code'    => $errorCode,
            'message' => $message,
            'data'    => $data,
        ];
        return response()->json($res, $errorCode);
    }

    /**
     * Trả về response theo định dạng của DNG
     * @param  mixed $data
     * @param  mixed $message
     * @param  mixed $type
     * @param  int $code
     * @return mixed
     */
    public static function dngResponse($data = null, $message = '', $type = '', $code = 200)
    {
        $res = [
            'Code'    => $code,
            'Type'    => $type,
            'Message' => $message,
            'data'    => $data,
        ];
        return response()->json($res, $code, ['Content-Type' => 'application/json; charset=utf-8']);
    }

    public static function HandleException(\Exception $exception)
    {
        $res = [
            'status'  => self::FAIL,
            'code'    => $exception->getCode(),
            'message' => $exception->getMessage(),
            'data'    => null,
        ];
        if ($exception instanceof ApiException) {
            if (env('APP_DEBUG') === true) {
                $res['file'] = $exception->getFile();
                $res['line'] = $exception->getLine();
            }
            if (env('API_DEBUG_TRACE') === true) {
                $res['trace'] = $exception->getTraceAsString();
            }
        }
        return response()->json($res);
    }

    public static function build($status = null, $errorCode = null, $message = '', $data = null)
    {
        $res = [
            'status'  => $status,
            'code'    => $errorCode,
            'message' => $message,
            'data'    => $data,
        ];
        if ($errorCode) return response()->json($res, $errorCode);
        return response()->json($res);
    }

    public static function plainText($data)
    {
        if (!is_string($data)) $data = json_encode($data);
        return response($data, 200)->header('Content-Type', 'text/plain');
    }
}
