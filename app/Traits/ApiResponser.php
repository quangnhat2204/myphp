<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponser
{
    /**
     * Build an error response.
     *
     * @param  string|null  $message
     * @param  int  $code
     * @param  array  $errors
     * @return \Illuminate\Http\JsonResponse
     */
    protected function error(?string $message = null, int $code, array $errors = []): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        // If there are validation errors, add them to the response
        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }
}