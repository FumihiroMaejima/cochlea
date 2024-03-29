<?php

declare(strict_types=1);

namespace App\Services\Users;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Http\Resources\Users\BannersResource;
use App\Repositories\Masters\Banners\BannersRepositoryInterface;
use App\Library\Array\ArrayLibrary;
use App\Library\Banner\BannerLibrary;
use App\Library\Cache\CacheLibrary;
use App\Library\File\FileLibrary;
use App\Models\Masters\Banners;
use \Symfony\Component\HttpFoundation\BinaryFileResponse;
use Exception;

class BannersService
{
    // cache keys
    private const CACHE_KEY_USER_BANNER_LIST = 'cache_user_banner_list';

    protected BannersRepositoryInterface $bannersRepository;

    /**
     * create service instance
     *
     * @param BannersRepositoryInterface $bannersRepository
     * @return void
     */
    public function __construct(BannersRepositoryInterface $bannersRepository)
    {
        $this->bannersRepository = $bannersRepository;
    }

    /**
     * get banners data
     *
     * @return array
     */
    public function getBanners(): array
    {
        $cache = CacheLibrary::getByKey(self::CACHE_KEY_USER_BANNER_LIST);

        // キャッシュチェック
        if (is_null($cache)) {
            $collection = $this->bannersRepository->getRecords();
            $resourceCollection = BannersResource::toArrayForGetCollectionList($collection);

            if (!empty($resourceCollection)) {
                CacheLibrary::setCache(self::CACHE_KEY_USER_BANNER_LIST, $resourceCollection);
            }
        } else {
            $resourceCollection = (array)$cache;
        }

        return $resourceCollection;
    }

    /**
     * 画像ファイルのダウンロード
     *
     * @param string $uuid
     * @return string file path
     * @throws MyApplicationHttpException
     */
    public function getImage(string $uuid): string
    {
        $banners = $this->bannersRepository->getByUuid($uuid, true);

        if (empty($banners)) {
            return BannerLibrary::getDefaultBannerStoragePath();
        }

        // 複数チェックはrepository側で実施済み
        $banner = ArrayLibrary::toArray(ArrayLibrary::getFirst($banners->toArray()));
        $bannerId = !empty($banner) ? $banner[Banners::ID] : 0;

        $imagePath = BannerLibrary::getBannerStoragePathByBannerIdAndUuid($bannerId, $uuid, 'png');

        $file = FileLibrary::getFileStoream($imagePath);

        if (is_null($file)) {
            return BannerLibrary::getDefaultBannerStoragePath();
        }

        return Storage::path($imagePath);
    }
}
