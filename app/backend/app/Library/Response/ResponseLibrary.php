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
     * @param ?array $data
     * @param string $message
     * @param int $status
     * @param array $headers
     * @return JsonResponse
     * @throws Exception
     */
    public static function jsonResponse(
        ?array $data = null,
        string $message = '',
        int $status = 200,
        array $headers = []
    ): JsonResponse {
        $dataParam = [
            'message' => $message,
            'status' => $status,
            'data' => $data
        ];
        if (!is_null($data)) {
            $dataParam = array_merge($dataParam, $data);
        }
        return response()->json(
            $dataParam,
            $status,
            $headers
        );
    }
}
