<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success(string $message, array $data = [], $statusCode = 200): JsonResponse
    {
        $response = [];
        $response['status'] = 'success';
        $response['message'] = $message;
        if (!empty($data)) {
            $response['data'] = $data;
        }
        return response()->json($response, $statusCode);
    }

    public static function failure(string $message, array $data = [], $statusCode = 400): JsonResponse
    {
        $response = [];
        $response['status'] = 'failure';
        $response['message'] = $message;
        if (!empty($data)) {
            $response['data'] = $data;
        }
        return response()->json($response, $statusCode);
    }
}
