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
use App\Http\Resources\Admins\ServiceTermsResource;
use App\Repositories\Masters\ServiceTerms\ServiceTermsRepositoryInterface;
use App\Exports\Masters\ServiceTerms\ServiceTermsBulkInsertTemplateExport;
use App\Exports\Masters\ServiceTerms\ServiceTermsExport;
use App\Imports\Masters\ServiceTerms\ServiceTermsImport;
use App\Library\Array\ArrayLibrary;
use App\Library\Cache\CacheLibrary;
use App\Library\Time\TimeLibrary;
use Exception;

class ServiceTermsService
{
    // cache keys
    private const CACHE_KEY_HOME_CONTENTS_COLLECTION_LIST = 'admin_home_contents_collection_list';

    protected ServiceTermsRepositoryInterface $serviceTermsRepository;

    /**
     * create service instance
     *
     * @param ServiceTermsRepositoryInterface $serviceTermsRepository
     * @return void
     */
    public function __construct(
        ServiceTermsRepositoryInterface $serviceTermsRepository,
    ) {
        $this->serviceTermsRepository = $serviceTermsRepository;
    }

    /**
     * download service terms data service
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadCSVForServiceTerms()
    {
        $data = $this->serviceTermsRepository->getRecords();

        return Excel::download(new ServiceTermsExport($data), 'service_terms_info_' . TimeLibrary::getCurrentDateTime(TimeLibrary::DATE_TIME_FORMAT_YMDHIS) . '.csv');
    }

    /**
     * download service terms template data service
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadTemplateForServiceTerms()
    {
        // ElocuentからCollectionを取得する時と異なり個別にcontent-typeを指定する必要がある。
        return Excel::download(
            new ServiceTermsBulkInsertTemplateExport(collect(Config::get('myappFile.service.admins.serviceTerms.template'))),
            'master_service_terms_template_' . TimeLibrary::getCurrentDateTime(TimeLibrary::DATE_TIME_FORMAT_YMDHIS) . '.csv',
            null,
            ['content-type' => 'text/csv; charset=UTF-8']
        );
    }


    /**
     * imort service terms by template data service
     *
     * @param UploadedFile $file
     * @return JsonResponse
     */
    public function importTemplateForServiceTerms(UploadedFile $file)
    {
        // ファイル名チェック
        if (!preg_match('/^master_service_terms_template_\d{14}\.csv/u', $file->getClientOriginalName())) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_422,
                'no include title.'
            );
        }

        DB::beginTransaction();
        try {
            $fileData = Excel::toArray(new ServiceTermsImport($file), $file, null, \Maatwebsite\Excel\Excel::CSV);

            $resource = ServiceTermsResource::toArrayForBulkInsert(current($fileData));

            $insertCount = $this->serviceTermsRepository->create($resource);

            DB::commit();

            // キャッシュの削除
            // CacheLibrary::deleteCache(self::CACHE_KEY_HOME_CONTENTS_COLLECTION_LIST, true);

            // レスポンスの制御
            $message = ($insertCount) ? 'success' : 'Bad Request';
            $status = ($insertCount) ? 201 : 401;

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
