<?php

namespace App\Http\Controllers\Users;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Http\Controllers\Controller;
use App\Services\Users\InformationsService;
use App\Trait\CheckHeaderTrait;

class InformationsController extends Controller
{
    use CheckHeaderTrait;
    private InformationsService $service;

    /**
     * Create a new controller instance.
     *
     * @param InformationsService $service
     * @return void
     */
    public function __construct(InformationsService $service)
    {
        $this->middleware('auth:api-users', ['except' => ['index']]);
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        // サービスの実行
        return $this->service->getInformations();
    }

    /**
     * create user read information request handling.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function createUserReadInformation(Request $request): JsonResponse
    {
        // バリデーションチェック
        $validator = Validator::make(
            $request->all(),
            [
                'informationId' => ['required','integer'],
            ]
        );

        if ($validator->fails()) {
            // $validator->errors()->toArray();
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_422,
            );
        }

        // ユーザーIDの取得
        $userId = $this->getUserId($request);

        // サービスの実行
        return $this->service->createUserReadInformation($userId, $request->informationId);
    }
}
