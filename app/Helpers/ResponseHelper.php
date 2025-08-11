<?php
namespace App\Helpers;

class ResponseHelper {
    public static function success($data = '', $message = '', $status = null, $statusCode = 200) {
        $statusCode = (int) $statusCode;
        return response()->json([
            'message' => $message,
            'data' => $data,
        ], $statusCode);
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