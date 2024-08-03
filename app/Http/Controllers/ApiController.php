<?php

namespace App\Http\Controllers;

abstract class ApiController
{
    public function success($data = [], $code = 200, $message = 'Success')
    {
        return response()->json([
            'data' => $data,
            'message' => $message
        ], $code);
    }

    public function error($message, $code = 404)
    {
        return response()->json([
            'message' => $message
        ], $code);
    }

    public function notFound($message = 'Not Found')
    {
        return $this->error($message);
    }
}