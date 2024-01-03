<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Http\Controllers\Controller;
use App\Services\Users\UserCoinHistoryService;
use App\Trait\CheckHeaderTrait;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class UserCoinHistoryController extends Controller
{
    use CheckHeaderTrait;
    private UserCoinHistoryService $service;

    /**
     * Create a Controller instance.
     *
     * @param UserCoinHistoryService $userCoinHistoryService
     * @return void
     */
    public function __construct(UserCoinHistoryService $userCoinHistoryService)
    {
        $this->middleware('auth:api-users');
        $this->service = $userCoinHistoryService;
    }

    /**
     * get user coin history list.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function getCoinHistoryList(Request $request): JsonResponse
    {
        // ユーザーIDの取得
        $userId = $this->getUserId($request);

        // サービスの実行
        return $this->service->getCoinHistory($userId);
    }

    /**
     * get user coin history single record by uuid.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $uuid uuid
     * @return JsonResponse
     */
    public function getCoinHistory(Request $request, string $uuid): JsonResponse
    {
        if ($uuid === '') {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_422,
            );
        }

        // ユーザーIDの取得
        $userId = $this->getUserId($request);

        // サービスの実行
        return $this->service->getCoinHistoryByUuid($userId, $request->uuid);
    }

    /**
     * get user coin history single record pdf by uuid.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $uuid uuid
     * @return BinaryFileResponse
     */
    public function getCoinHistoryPdf(Request $request, string $uuid): BinaryFileResponse
    {
        if ($uuid === '') {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_422,
            );
        }

        // ユーザーIDの取得
        $userId = $this->getUserId($request);

        // サービスの実行
        return $this->service->getCoinHistoryPdfByUuid($userId, $request->uuid);
    }
}
