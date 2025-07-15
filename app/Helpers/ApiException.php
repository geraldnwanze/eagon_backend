<?php

namespace App\Helpers;

use Throwable;

class ApiException
{
    public static function handle($request, Throwable $exception)
    {
        $response = [
            'success' => false,
            'error' => [
                'message' => $exception->getMessage(),
                'status' => method_exists($exception, 'getCode') ? $exception->getCode() : 400,
            ]
        ];

        // Apply further customization to the response based on the type of exception
        // For example, a 404 error
        if ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            $response['error']['message'] = 'Resource not found';
            $response['error']['status'] = 404;
        }

        // The HTTP status code might be set differently based on the exception
        $statusCode = $response['error']['status'];

        return ApiResponse::failure('Something went wrong', $response, $statusCode);
    }
}
