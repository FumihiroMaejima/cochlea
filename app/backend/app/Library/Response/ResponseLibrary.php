<?php

declare(strict_types=1);

namespace App\Library\Response;

use Exception;
use Illuminate\Http\JsonResponse;
use \Symfony\Component\HttpFoundation\BinaryFileResponse;

class ResponseLibrary
{
    /**
     * json形式のレスポンスの整形
     *
     * @param array $data
     * @param string $message
     * @param int $status
     * @param array $headers
     * @return JsonResponse
     * @throws Exception
     */
    public static function jsonResponse(
        array $data,
        string $message = '',
        int $status = 200,
        array $headers = []
    ): JsonResponse {
        return response()->json(
            [
                'message' => $message,
                'status' => $status,
                'data' => $data
            ],
            $status,
            $headers
        );
    }
}
