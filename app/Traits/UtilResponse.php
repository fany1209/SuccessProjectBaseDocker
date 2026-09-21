<?php

namespace App\Traits;

class UtilResponse
{
    function successResponse($data = [], $message = 'Success Operation', $code = 200)
    {
        return response()->json(
            [
                "success" => true,
                "flag"    => true,
                "code"    => $code,
                "message" => $message,
                "data"    => $data
            ],
            $code
        );
    }

    function errorResponse($message = 'Error Ocurred', $code = 404)
    {
        return response()->json(
            [
                "success" => false,
                "flag"    => false,
                'code'    => $code,
                "message" => $message,
                "data"    => []
            ],
            $code
        );
    }
}