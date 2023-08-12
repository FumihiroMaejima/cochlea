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
use App\Http\Requests\User\ServiceTerms\UserServiceTermsCreateRequest;
use App\Services\Users\ServiceTermsService;
use App\Trait\CheckHeaderTrait;

class ServiceTermsController extends Controller
{
    use CheckHeaderTrait;
    private ServiceTermsService $service;

    /**
     * Create a new controller instance.
     *
     * @param ServiceTermsService $service
     * @return void
     */
    public function __construct(ServiceTermsService $service)
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
        return $this->service->getLatestServiceTerms();
    }

    /**
     * create user service term request handling.
     *
     * @param UserServiceTermsCreateRequest $request
     * @param int $informationId
     * @return JsonResponse
     */
    public function createUserServiceTerm(UserServiceTermsCreateRequest $request): JsonResponse
    {
        // ユーザーIDの取得
        $userId = self::getUserId($request);

        // サービスの実行
        return $this->service->createUserServiceTerm($userId, $request->{UserServiceTermsCreateRequest::KEY_ID});
    }
}
