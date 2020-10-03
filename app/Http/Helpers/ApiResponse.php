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
     * Note: $errors param expected to be Illuminate\Support\MessageBag object
     * or an array in this form
     * [
     * "error_key":[
     *      "err_msg_1",
     *      "err_msg_2"
     * ]
     * ]
     *
     */
    public function sendResponse($status, $message, $data = null, $errors = null)
    {

        $errors = $errors instanceof MessageBag ? $errors->messages() : $errors;

        $errorsList = [];

        if($errors){

            foreach ($errors as $errorKey => $error){
                $errorList = [$errorKey => $error];
                $errorsList[] = $errorList;
            }

        }

        $errorsList = !empty($errorsList) ? $errorsList : null;

        return response()->json([
            "message" => $message,
            "data" => $data,
            "errors" => $errorsList
        ], $status);
    }
}
