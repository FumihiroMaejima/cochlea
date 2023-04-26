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
use App\Http\Resources\Admins\BannerBlockContentsResource;
use App\Http\Resources\Admins\BannerBlocksResource;
use App\Repositories\Masters\Banners\BannersBlockContentsRepositoryInterface;
use App\Repositories\Masters\Banners\BannersBlocksRepositoryInterface;
use App\Exports\Masters\BannerBlocks\BannerBlockContentsBulkInsertTemplateExport;
use App\Exports\Masters\BannerBlocks\BannerBlockContentsExport;
use App\Exports\Masters\BannerBlocks\BannerBlocksBulkInsertTemplateExport;
use App\Exports\Masters\BannerBlocks\BannerBlocksExport;
use App\Imports\Masters\BannerBlocks\BannerBlockContentsImport;
use App\Imports\Masters\BannerBlocks\BannerBlocksImport;
use App\Library\Array\ArrayLibrary;
use App\Library\Cache\CacheLibrary;
use Exception;

class BannerContentsService
{
    // cache keys
    private const CACHE_KEY_BANNER_BLOCK_CONTENTS_COLLECTION_LIST = 'admin_banner_block_contents_collection_list';

    protected BannersBlocksRepositoryInterface $bannerBlocksRepository;
    protected BannersBlockContentsRepositoryInterface $bannerBlockContentsRepository;

    /**
     * create service instance
     *
     * @param BannersBlocksRepositoryInterface $bannerBlocksRepository
     * @param BannersBlockContentsRepositoryInterface $bannerBlockContentsRepository
     * @return void
     */
    public function __construct(
        BannersBlocksRepositoryInterface $bannerBlocksRepository,
        BannersBlockContentsRepositoryInterface $bannerBlockContentsRepository,
    ) {
        $this->bannerBlocksRepository = $bannerBlocksRepository;
        $this->bannerBlockContentsRepository = $bannerBlockContentsRepository;
    }

    /**
     * download banner blocks groups data service
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadCSVForBannerBlocks()
    {
        $data = $this->bannerBlocksRepository->getRecords();

        return Excel::download(new BannerBlocksExport($data), 'banner_banner_blocks_info_' . Carbon::now()->format('YmdHis') . '.csv');
    }

    /**
     * download banner blocks groups template data service
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadTemplateForBannerBlocks()
    {
        return Excel::download(
            new BannerBlocksBulkInsertTemplateExport(collect(Config::get('myappFile.service.admins.bannerBlocks.template'))),
            'master_banner_blocks_template_' . Carbon::now()->format('YmdHis') . '.csv'
        );
    }


    /**
     * imort banner blocks by template data service
     *
     * @param UploadedFile $file
     * @return JsonResponse
     */
    public function importTemplateForBannerBlocks(UploadedFile $file)
    {
        // ファイル名チェック
        if (!preg_match('/^master_banner_blocks_template_\d{14}\.csv/u', $file->getClientOriginalName())) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_422,
                'no include title.'
            );
        }

        DB::beginTransaction();
        try {
            $fileData = Excel::toArray(new BannerBlocksImport($file), $file, null, \Maatwebsite\Excel\Excel::CSV);

            $resource = BannerBlocksResource::toArrayForBulkInsert(current($fileData));

            $insertCount = $this->bannerBlocksRepository->create($resource);

            DB::commit();

            // キャッシュの削除
            // CacheLibrary::deleteCache(self::CACHE_KEY_BANNER_BLOCK_CONTENTS_COLLECTION_LIST, true);

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

    /**
     * download banner block contents data service
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadCSVForBannerBlockContents()
    {
        $data = $this->bannerBlockContentsRepository->getRecords();

        return Excel::download(new BannerBlockContentsExport($data), 'banner_block_contents_info_' . Carbon::now()->format('YmdHis') . '.csv');
    }

    /**
     * download banner block contents template data service
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadTemplateForBannerBlockContents()
    {
        return Excel::download(
            new BannerBlockContentsBulkInsertTemplateExport(collect(Config::get('myappFile.service.admins.bannerBlockContents.template'))),
            'master_banner_block_contents_template_' . Carbon::now()->format('YmdHis') . '.csv'
        );
    }


    /**
     * imort banner block contents by template data service
     *
     * @param UploadedFile $file
     * @return JsonResponse
     */
    public function importTemplateForBannerBlockContents(UploadedFile $file)
    {
        // ファイル名チェック
        if (!preg_match('/^master_banner_block_contents_template_\d{14}\.csv/u', $file->getClientOriginalName())) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_422,
                'no include title.'
            );
        }

        DB::beginTransaction();
        try {
            $fileData = Excel::toArray(new BannerBlockContentsImport($file), $file, null, \Maatwebsite\Excel\Excel::CSV);

            $resource = BannerBlockContentsResource::toArrayForBulkInsert(current($fileData));

            $insertCount = $this->bannerBlockContentsRepository->create($resource);

            DB::commit();

            // キャッシュの削除
            // CacheLibrary::deleteCache(self::CACHE_KEY_BANNER_BLOCK_CONTENTS_COLLECTION_LIST, true);

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
