<?php

namespace App\Helpers;


use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * @param object $payload This is the array of objects to be returned to the user
     * @param string $message This is the message string returned to the user
     * @return JsonResponse
     */
    public function sendResponse(object $payload, string $message) {
        $response = [
            'success' => true,
            'payload'    => $payload,
            'message' => $message,
        ];
        return response()->json($response, 200);
    }


    /**
     * @param string $error Single error message returned on failure
     * @param array $errorMessages Array of error messages when multiple
     * @param int $code Error code returned to user
     * @return JsonResponse
     */
    public function sendError(string $error, $errorMessages = [], $code = 403)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];


        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }
        return response()->json($response, $code);
    }
}
