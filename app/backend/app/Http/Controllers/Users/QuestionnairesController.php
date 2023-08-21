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
        return $this->service->getQuestionnaires();
    }

    /**
     * create user questionnaires request handling.
     *
     * @param UserQuestionnairesCreateRequest $request
     * @param int $informationId
     * @return JsonResponse
     */
    public function createUserQuestionnaire(UserQuestionnairesCreateRequest $request): JsonResponse
    {
        // ユーザーIDの取得
        $userId = self::getUserId($request);

        // サービスの実行
        return $this->service->createUserQuestionnaire(
            $userId,
            $request->{UserQuestionnairesCreateRequest::KEY_ID},
            $request->{UserQuestionnairesCreateRequest::KEY_QUESTIONS}
        );
    }

    /**
     * update user questionnaires request handling.
     *
     * @param UserQuestionnairesUpdateRequest $request
     * @param int $informationId
     * @return JsonResponse
     */
    public function updateUserQuestionnaire(UserQuestionnairesUpdateRequest $request): JsonResponse
    {
        // ユーザーIDの取得
        $userId = self::getUserId($request);

        // サービスの実行
        return $this->service->updateUserQuestionnaire(
            $userId,
            $request->{UserQuestionnairesUpdateRequest::KEY_ID},
            $request->{UserQuestionnairesUpdateRequest::KEY_QUESTIONS}
        );
    }
}
