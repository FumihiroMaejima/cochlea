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
use App\Http\Requests\User\Questionnaires\UserQuestionnairesCreateRequest;
use App\Http\Requests\User\Questionnaires\UserQuestionnairesUpdateRequest;
use App\Services\Users\QuestionnairesService;
use App\Trait\CheckHeaderTrait;

class QuestionnairesController extends Controller
{
    use CheckHeaderTrait;
    private QuestionnairesService $service;

    /**
     * Create a new controller instance.
     *
     * @param QuestionnairesService $service
     * @return void
     */
    public function __construct(QuestionnairesService $service)
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
        return ResponseLibrary::jsonResponse($this->service->getQuestionnaires());
    }

    /**
     * Display of the resource.
     *
     * @param Request $request
     * @param int $questionnaireId
     * @return JsonResponse
     * @throws MyApplicationHttpException
     */
    public function detail(Request $request, int $questionnaireId): JsonResponse
    {
        // サービスの実行
        return ResponseLibrary::jsonResponse(
            $this->service->getQuestionnaire(self::getUserId($request), $questionnaireId)
        );
    }

    /**
     * create user questionnaires request handling.
     *
     * @param UserQuestionnairesCreateRequest $request
     * @param int $informationId
     * @return JsonResponse
     * @throws MyApplicationHttpException
     */
    public function createUserQuestionnaire(UserQuestionnairesCreateRequest $request): JsonResponse
    {
        // ユーザーIDの取得
        $userId = self::getUserId($request);

        // サービスの実行
        $this->service->createUserQuestionnaire(
            $userId,
            (int)$request->{UserQuestionnairesCreateRequest::KEY_ID},
            $request->{UserQuestionnairesCreateRequest::KEY_QUESTIONS}
        );
        return ResponseLibrary::jsonResponse(status: StatusCodeMessages::STATUS_201);
    }

    /**
     * update user questionnaires request handling.
     *
     * @param UserQuestionnairesUpdateRequest $request
     * @param int $informationId
     * @return JsonResponse
     * @throws MyApplicationHttpException
     */
    public function updateUserQuestionnaire(UserQuestionnairesUpdateRequest $request): JsonResponse
    {
        // ユーザーIDの取得
        $userId = self::getUserId($request);

        // サービスの実行
        $this->service->updateUserQuestionnaire(
            $userId,
            (int)$request->{UserQuestionnairesUpdateRequest::KEY_ID},
            $request->{UserQuestionnairesUpdateRequest::KEY_QUESTIONS}
        );
        return ResponseLibrary::jsonResponse();
    }
}
