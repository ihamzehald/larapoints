<?php

namespace App\Http\Helpers;

/**
 * Trait ApiResponse
 * @package App\Http\Helpers
 * All helpers that related to API response
 */
trait ApiResponse
{

    /**
     * Standard method to send API response
     * @param $status integer as response status
     * @param $message string as response message
     * @param $data array/null as response data
     * @param null $errors
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResponse($status, $message, $data = null, $errors = null)
    {
        return response()->json([
            "message" => $message,
            "data" => $data,
            "errors" => $errors
        ], $status);
    }
}
