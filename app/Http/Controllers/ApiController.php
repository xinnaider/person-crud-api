<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(title="Person CRUD with Auth", version="0.1")
 *
 * @OAS\SecurityScheme(
 *    securityScheme="bearer_token",
 *     type="http",
 *     scheme="bearer"
 * )
 */
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
