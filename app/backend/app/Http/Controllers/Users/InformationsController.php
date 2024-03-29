<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Library\Response\ResponseLibrary;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\Informations\UserReadInformationCreateRequest;
use App\Http\Requests\User\Informations\UserReadInformationDeleteRequest;
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
     * @throws MyApplicationHttpException
     */
    public function index(): JsonResponse
    {
        // サービスの実行
        return ResponseLibrary::jsonResponse($this->service->getInformations());
    }

    /**
     * create user read information request handling.
     *
     * @param UserReadInformationCreateRequest $request
     * @param int $informationId
     * @return JsonResponse
     * @throws MyApplicationHttpException
     */
    public function createUserReadInformation(UserReadInformationCreateRequest $request): JsonResponse
    {
        // ユーザーIDの取得
        $userId = self::getUserId($request);

        // サービスの実行
        $this->service->createUserReadInformation(
            $userId,
            (int)$request->{UserReadInformationCreateRequest::KEY_ID}
        );
        return ResponseLibrary::jsonResponse(status: StatusCodeMessages::STATUS_201);
    }

    /**
     * delete user read information request handling.
     *
     * @param UserReadInformationDeleteRequest $request
     * @param int $informationId
     * @return JsonResponse
     * @throws MyApplicationHttpException
     */
    public function deleteUserReadInformation(UserReadInformationDeleteRequest $request): JsonResponse
    {
        // ユーザーIDの取得
        $userId = self::getUserId($request);

        // サービスの実行
        $this->service->removeUserReadInformation(
            $userId,
            (int)$request->{UserReadInformationDeleteRequest::KEY_ID}
        );
        return ResponseLibrary::jsonResponse();
    }
}
