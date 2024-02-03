<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admins;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Library\Response\ResponseLibrary;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Questionnaires\QuestionnairesImportRequest;
use App\Services\Admins\QuestionnairesService;
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
        $this->middleware('customAuth:api-admins');
        $this->service = $service;
    }

    /**
     * download a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws MyApplicationHttpException
     */
    public function downloadQuestionnaires(Request $request): BinaryFileResponse
    {
        // 権限チェック
        if (!$this->checkRequestAuthority($request, Config::get('myapp.executionRole.services.questionnaires'))) {
            throw new MyApplicationHttpException(StatusCodeMessages::STATUS_403);
        }

        // サービスの実行
        return $this->service->downloadCSVForQuestionnaires();
    }

    /**
     * download import template for import the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws MyApplicationHttpException
     */
    public function templateQuestionnaires(Request $request): BinaryFileResponse
    {
        // 権限チェック
        if (!$this->checkRequestAuthority($request, Config::get('myapp.executionRole.services.questionnaires'))) {
            throw new MyApplicationHttpException(StatusCodeMessages::STATUS_403);
        }

        // サービスの実行
        return $this->service->downloadTemplateForQuestionnaires();
    }

    /**
     * import record data by file.
     *
     * @param QuestionnairesImportRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws MyApplicationHttpException
     */
    public function uploadTemplateQuestionnaires(QuestionnairesImportRequest $request): JsonResponse
    {
        // サービスの実行
        $this->service->importTemplateForQuestionnaires($request->file);
        return ResponseLibrary::jsonResponse(status: StatusCodeMessages::STATUS_201);
    }
}
