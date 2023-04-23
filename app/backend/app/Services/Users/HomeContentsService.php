<?php

namespace App\Services\Users;

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
use App\Http\Resources\Users\HomeContentsResource;
use App\Repositories\Admins\HomeContents\HomeContentsGroupsRepositoryInterface;
use App\Repositories\Admins\HomeContents\HomeContentsRepositoryInterface;
use App\Repositories\Admins\Banners\BannersBlockContentsRepositoryInterface;
use App\Repositories\Admins\Banners\BannersBlocksRepositoryInterface;
use App\Library\Array\ArrayLibrary;
use App\Library\Cache\CacheLibrary;
use App\Models\Masters\HomeContentsGroups;
use App\Models\Masters\HomeContents;
use Exception;

class HomeContentsService
{
    // cache keys
    private const CACHE_KEY_USER_HOME_CONTENTS_GROUP_LIST = 'cache_user_home_contents_group_list';
    private const CACHE_KEY_USER_HOME_CONTENTS_LIST = 'cache_user_home_contents_list';
    private const CACHE_KEY_USER_BANNER_BLOCKS_LIST = 'cache_user_banner_blocks_list';
    private const CACHE_KEY_USER_BANNER_BLOCKS_CONTENTS_LIST = 'cache_user_banner_blocks_contents_list';

    protected HomeContentsGroupsRepositoryInterface $homeContentsGroupsRepository;
    protected HomeContentsRepositoryInterface $homeContentsRepository;
    protected BannersBlocksRepositoryInterface $bannerBlocksRepository;
    protected BannersBlockContentsRepositoryInterface $bannerBlockContentsRepository;

    /**
     * create service instance
     *
     * @param HomeContentsGroupsRepositoryInterface $homeContentsGroupsRepository
     * @param HomeContentsRepositoryInterface $homeContentsRepository
     * @param BannersBlocksRepositoryInterface $bannerBlocksRepository
     * @param BannersBlockContentsRepositoryInterface $bannerBlockContentsRepository
     * @return void
     */
    public function __construct(
        HomeContentsGroupsRepositoryInterface $homeContentsGroupsRepository,
        HomeContentsRepositoryInterface $homeContentsRepository,
        BannersBlocksRepositoryInterface $bannerBlocksRepository,
        BannersBlockContentsRepositoryInterface $bannerBlockContentsRepository,
        )
    {
        $this->homeContentsGroupsRepository = $homeContentsGroupsRepository;
        $this->homeContentsRepository = $homeContentsRepository;
        $this->bannerBlocksRepository = $bannerBlocksRepository;
        $this->bannerBlockContentsRepository = $bannerBlockContentsRepository;
    }

    /**
     * get home contents data
     *
     * @return JsonResponse
     */
    public function getHomeContents(): JsonResponse
    {
        $response = [];

        $homeGroups = $this->getHomeContentsGroupsRecords();
        $homeGroupsContents = $this->getHomeContentsRecords(1);
        $bennerBlocks = $this->getBannerBlocksRecords([]);
        $bannerBlockContents = $this->getBannerBlocksContentsRecords(1);

        return response()->json($response, 200);
    }

    /**
     * get home contents groups records
     *
     * @return array
     */
    private function getHomeContentsGroupsRecords(): array
    {
        $cache = CacheLibrary::getByKey(self::CACHE_KEY_USER_HOME_CONTENTS_GROUP_LIST);

        // キャッシュチェック
        if (is_null($cache)) {
            $collection = $this->homeContentsGroupsRepository->getRecords();
            if (empty($collection)) {
                return [];
            }
            $records = ArrayLibrary::toArray($collection->toArray());

            if (!empty($records)) {
                CacheLibrary::setCache(self::CACHE_KEY_USER_HOME_CONTENTS_GROUP_LIST, $records);
            }
        } else {
            $records = $cache;
        }

        return $records;
    }

    /**
     * get home contents records
     *
     * @param int $groupId
     * @return array
     */
    private function getHomeContentsRecords(int $groupId): array
    {
        $cache = CacheLibrary::getByKey(self::CACHE_KEY_USER_HOME_CONTENTS_LIST);

        // キャッシュチェック
        if (is_null($cache)) {
            $records = $this->getHomeContentesByGroupId($groupId);

            if (!empty($records)) {
                CacheLibrary::setCache(self::CACHE_KEY_USER_HOME_CONTENTS_LIST, $records);
            }
        } else {
            $records = $cache;
        }

        return $records;
    }

    /**
     * get banner blocks records
     *
     * @param array $bannerBlockIds
     * @return array
     */
    private function getBannerBlocksRecords(array $bannerBlockIds): array
    {
        $cache = CacheLibrary::getByKey(self::CACHE_KEY_USER_BANNER_BLOCKS_LIST);

        // キャッシュチェック
        if (is_null($cache)) {
            $records = $this->getBannerBlocksByIds($bannerBlockIds);

            if (!empty($records)) {
                CacheLibrary::setCache(self::CACHE_KEY_USER_BANNER_BLOCKS_LIST, $records);
            }
        } else {
            $records = $cache;
        }

        return $records;
    }

    /**
     * get banner blocks contents records
     *
     * @param int $bannerBlockId
     * @return array
     */
    private function getBannerBlocksContentsRecords(int $bannerBlockId): array
    {
        $cache = CacheLibrary::getByKey(self::CACHE_KEY_USER_BANNER_BLOCKS_CONTENTS_LIST);

        // キャッシュチェック
        if (is_null($cache)) {
            $records = $this->getBannerBlockContentsByBlockId($bannerBlockId);

            if (!empty($records)) {
                CacheLibrary::setCache(self::CACHE_KEY_USER_BANNER_BLOCKS_CONTENTS_LIST, $records);
            }
        } else {
            $records = $cache;
        }

        return $records;
    }

    /**
     * get home contents by group id.
     *
     * @param int $groupId group id
     * @return array
     */
    private function getHomeContentesByGroupId(int $groupId): array
    {
        $records = $this->homeContentsRepository->getByGroupId($groupId, true);

        if (empty($records)) {
            return [];
        }

        // 複数チェックはrepository側で実施済み
        return ArrayLibrary::toArray($records->toArray());
    }

    /**
     * get banner blocks by ids.
     *
     * @param array $ids records id
     * @return array
     */
    private function getBannerBlocksByIds(array $ids): array
    {
        $records = $this->bannerBlocksRepository->getByIds($ids, true);

        if (empty($records)) {
            return [];
        }

        // 複数チェックはrepository側で実施済み
        return ArrayLibrary::toArray($records->toArray());
    }

    /**
     * get banner block contents by banner block id.
     *
     * @param array $ids block records id
     * @return array
     */
    private function getBannerBlockContentsByBlockId(int $id): array
    {
        $records = $this->bannerBlockContentsRepository->getByBlockId($id, true);

        if (empty($records)) {
            return [];
        }

        // 複数チェックはrepository側で実施済み
        return ArrayLibrary::toArray($records->toArray());
    }
}