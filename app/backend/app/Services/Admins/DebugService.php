<?php

declare(strict_types=1);

namespace App\Services\Admins;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Http\Requests\Admin\Debug\DebugFileUploadRequest;
use App\Library\Stripe\StripeLibrary;
use App\Library\Time\TimeLibrary;
use App\Library\String\UuidLibrary;
use App\Models\Masters\Admins;

class DebugService
{
    protected string $prop;

    /**
     * create DebugService instance
     *
     * @return void
     */
    public function __construct()
    {
        $this->prop = 'debug propaty';
    }

    /**
     * get permissions data for frontend parts
     *
     * @param  \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function getList(): JsonResponse
    {
        return StripeLibrary::getTestList();
    }

    /**
     * デバッグ関連情報取得と整形
     *
     * @param int $userId
     * @param string $sessionId
     * @param ?int $fakerTimeStamp
     * @param ?string $clinetIp
     * @param ?string $userAgent
     * @return JsonResponse
     * @throws MyApplicationHttpException
     */
    public function getDebugStatus(
        int $userId,
        string $sessionId,
        ?int $fakerTimeStamp,
        ?string $clinetIp,
        ?string $userAgent
    ): JsonResponse {
        $admin = (new Admins())->getRecordById($userId);
        if (is_null($admin)) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_404,
                'not admin exist.'
            );
        }

        $response = [
            'userId' => $userId,
            'sessionId' => $sessionId,
            'email' => $admin[Admins::EMAIL],
            'name' => $admin[Admins::NAME],
            'fakerTimeStamp' => $fakerTimeStamp,
            'host' => config('app.url'),
            'clinetIp' => $clinetIp,
            'userAgent' => $userAgent,
        ];

        return response()->json(['data' => $response, 'status' => 200]);
    }
}
