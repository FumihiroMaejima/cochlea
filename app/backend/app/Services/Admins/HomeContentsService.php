<?php

namespace App\Services\Admins;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Http\Resources\Admins\HomeContentsGroupsResource;
use App\Http\Resources\Admins\HomeContentsResource;
use App\Repositories\Admins\HomeContents\HomeContentsGroupsRepositoryInterface;
use App\Repositories\Admins\HomeContents\HomeContentsRepositoryInterface;
use App\Exports\Masters\HomeContents\HomeContentsBulkInsertTemplateExport;
use App\Exports\Masters\HomeContents\HomeContentsExport;
use App\Exports\Masters\HomeContents\HomeContentsGroupsBulkInsertTemplateExport;
use App\Exports\Masters\HomeContents\HomeContentsGroupsExport;
use App\Imports\Masters\HomeContents\HomeContentsGroupsImport;
use App\Imports\Masters\HomeContents\HomeContentsImport;
use App\Library\Array\ArrayLibrary;
use App\Library\Cache\CacheLibrary;
use App\Models\Masters\HomeContentsGroups;
use App\Models\Masters\HomeContents;
use Exception;

class HomeContentsService
{
    // cache keys
    private const CACHE_KEY_HOME_CONTENTS_COLLECTION_LIST = 'admin_home_contents_collection_list';

    protected HomeContentsGroupsRepositoryInterface $homeContentsGroupsRepository;
    protected HomeContentsRepositoryInterface $homeContentsRepository;

    /**
     * create service instance
     *
     * @param HomeContentsGroupsRepositoryInterface $homeContentsGroupsRepository
     * @param HomeContentsRepositoryInterface $homeContentsRepository
     * @return void
     */
    public function __construct(
        HomeContentsGroupsRepositoryInterface $homeContentsGroupsRepository,
        HomeContentsRepositoryInterface $homeContentsRepository,
        )
    {
        $this->homeContentsGroupsRepository = $homeContentsGroupsRepository;
        $this->homeContentsRepository = $homeContentsRepository;
    }

    /**
     * download home contents groups data service
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadCSVForHomeContentsGroups()
    {
        $data = $this->homeContentsGroupsRepository->getRecords();

        return Excel::download(new HomeContentsGroupsExport($data), 'home_contents_groups_info_' . Carbon::now()->format('YmdHis') . '.csv');
    }

    /**
     * download home contents groups template data service
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadTemplateForHomeContentsGroups()
    {
        return Excel::download(
            new HomeContentsGroupsBulkInsertTemplateExport(collect(Config::get('myappFile.service.admins.homeContentsGroups.template'))),
            'master_home_contents_groups_template_' . Carbon::now()->format('YmdHis') . '.csv'
        );
    }


    /**
     * imort home contents groups by template data service
     *
     * @param UploadedFile $file
     * @return JsonResponse
     */
    public function importTemplateForHomeContentsGroups(UploadedFile $file)
    {
        // ファイル名チェック
        if (!preg_match('/^master_home_contents_groups_template_\d{14}\.csv/u', $file->getClientOriginalName())) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_422,
                'no include title.'
            );
        }

        DB::beginTransaction();
        try {
            $fileData = Excel::toArray(new HomeContentsGroupsImport($file), $file, null, \Maatwebsite\Excel\Excel::CSV);

            $resource = HomeContentsGroupsResource::toArrayForBulkInsert(current($fileData));

            $insertCount = $this->homeContentsGroupsRepository->create($resource);

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
            abort(500);
        }
    }

    /**
     * download home contents data service
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadCSVForHomeContents()
    {
        $data = $this->homeContentsRepository->getRecords();

        return Excel::download(new HomeContentsExport($data), 'home_contents_info_' . Carbon::now()->format('YmdHis') . '.csv');
    }

    /**
     * download home contents template data service
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadTemplateForHomeContents()
    {
        return Excel::download(
            new HomeContentsBulkInsertTemplateExport(collect(Config::get('myappFile.service.admins.homeContents.template'))),
            'master_home_contents_template_' . Carbon::now()->format('YmdHis') . '.csv'
        );
    }


    /**
     * imort home contents by template data service
     *
     * @param UploadedFile $file
     * @return JsonResponse
     */
    public function importTemplateForHomeContents(UploadedFile $file)
    {
        // ファイル名チェック
        if (!preg_match('/^master_home_contents_template_\d{14}\.csv/u', $file->getClientOriginalName())) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_422,
                'no include title.'
            );
        }

        DB::beginTransaction();
        try {
            $fileData = Excel::toArray(new HomeContentsImport($file), $file, null, \Maatwebsite\Excel\Excel::CSV);

            $resource = HomeContentsResource::toArrayForBulkInsert(current($fileData));

            $insertCount = $this->homeContentsRepository->create($resource);

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
            abort(500);
        }
    }
}
