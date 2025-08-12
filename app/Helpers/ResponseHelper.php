<?php
namespace App\Helpers;

class ResponseHelper {
    public static function success($data = '', $message = '', $status = null, $statusCode = 200) {
        $statusCode = (int) $statusCode;
        $response = [
            'message' => $message,
        ];

        // Only add data if it's not null
        if (!is_null($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }
    
     public static function error($data = '', $message = '', $status = '', $statusCode = 400) {
        $statusCode = (int) $statusCode;
        return response()->json([
            'status' => $status,
            'message' => $message,
            'error' => $data,
        ], $statusCode);
    }
}