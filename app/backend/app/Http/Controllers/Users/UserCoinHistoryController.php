<?php

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
     * Create Stipe Checkout Session.
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
}
