<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    /**
     * Standard success JSON response.
     */
    protected function successResponse(mixed $data = null, string $message = 'Success', int $statusCode = 200): JsonResponse
    {
        $payload = [
            'success' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $payload['data'] = $data;
        }

        return response()->json($payload, $statusCode);
    }

    /**
     * Standard error JSON response.
     */
    protected function errorResponse(string $message = 'An error occurred', int $statusCode = 400, mixed $errors = null): JsonResponse
    {
        $payload = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $statusCode);
    }
}

