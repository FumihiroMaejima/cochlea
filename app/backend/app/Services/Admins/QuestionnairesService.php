<?php

namespace App\Services\Admins;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Http\Resources\Admins\QuestionnairesResource;
use App\Repositories\Masters\Questionnaires\QuestionnairesRepositoryInterface;
use App\Exports\Masters\Questionnaires\QuestionnairesBulkInsertTemplateExport;
use App\Exports\Masters\Questionnaires\QuestionnairesExport;
use App\Imports\Masters\Questionnaires\QuestionnairesImport;
use App\Library\Array\ArrayLibrary;
use App\Library\Cache\CacheLibrary;
use App\Library\Time\TimeLibrary;
use Exception;

class QuestionnairesService
{
    // cache keys
    private const CACHE_KEY_COLLECTION_LIST = '';

    protected QuestionnairesRepositoryInterface $questionnairesRepository;

    /**
     * create service instance
     *
     * @param QuestionnairesRepositoryInterface $questionnairesRepository
     * @return void
     */
    public function __construct(
        QuestionnairesRepositoryInterface $questionnairesRepository,
    ) {
        $this->questionnairesRepository = $questionnairesRepository;
    }

    /**
     * download questionnaires data service
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadCSVForQuestionnaires()
    {
        $data = $this->questionnairesRepository->getRecords();

        return Excel::download(new QuestionnairesExport($data), 'questionnaires_info_' . TimeLibrary::getCurrentDateTime(TimeLibrary::DATE_TIME_FORMAT_YMDHIS) . '.csv');
    }

    /**
     * download questionnaires template data service
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadTemplateForQuestionnaires()
    {
        // ElocuentからCollectionを取得する時と異なり個別にcontent-typeを指定する必要がある。
        return Excel::download(
            new QuestionnairesBulkInsertTemplateExport(collect(Config::get('myappFile.service.admins.questionnaires.template'))),
            'master_questionnaires_template_' . TimeLibrary::getCurrentDateTime(TimeLibrary::DATE_TIME_FORMAT_YMDHIS) . '.csv',
            null,
            ['content-type' => 'text/csv; charset=UTF-8']
        );
    }


    /**
     * imort questionnaires by template data service
     *
     * @param UploadedFile $file
     * @return JsonResponse
     */
    public function importTemplateForQuestionnaires(UploadedFile $file)
    {
        // ファイル名チェック
        if (!preg_match('/^master_questionnaires_template_\d{14}\.csv/u', $file->getClientOriginalName())) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_422,
                'no include title.'
            );
        }

        DB::beginTransaction();
        try {
            $fileData = Excel::toArray(new QuestionnairesImport($file), $file, null, \Maatwebsite\Excel\Excel::CSV);

            $resource = QuestionnairesResource::toArrayForBulkInsert(current($fileData));

            $insertCount = $this->questionnairesRepository->create($resource);

            DB::commit();

            // キャッシュの削除
            // CacheLibrary::deleteCache(self::CACHE_KEY_HOME_CONTENTS_COLLECTION_LIST, true);

            // レスポンスの制御
            $message = ($insertCount > 0) ? 'success' : 'Bad Request';
            $status = ($insertCount > 0) ? 201 : 401;

            return response()->json(['message' => $message, 'status' => $status], $status);
        } catch (Exception $e) {
            Log::error(__CLASS__ . '::' . __FUNCTION__ . ' line:' . __LINE__ . ' ' . 'message: ' . json_encode($e->getMessage()));
            DB::rollback();
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_500,
                $e->getMessage(),
                previous: $e
            );
        }
    }
}
