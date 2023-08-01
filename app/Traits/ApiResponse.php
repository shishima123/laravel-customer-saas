<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    public function successResponse(string $message = null, $data = null, int $code = 200): JsonResponse
    {
        return response()->json($this->setResourceResponse($message, $data, $code), $code);
    }

    public function errorResponse(string $message = null, $data = null, int $code = 400): JsonResponse
    {
        return response()->json($this->setResourceResponse($message, $data, $code), $code);
    }

    public function customResponse($jsonError = [], $statusCode = 200): JsonResponse
    {
        return response()->json($jsonError, $statusCode);
    }

    private function setResourceResponse(string $message, $data, int $code): array
    {
        $rs = [
            'status' => $code,
            'message' => $message
        ];
        if (!is_null($data)) {
            $rs = array_merge($rs, ['data' => $data]);
        }
        return $rs;
    }
}
