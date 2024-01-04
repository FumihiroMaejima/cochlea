<?php

declare(strict_types=1);

namespace App\Http\Resources\Admins;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use App\Library\Time\TimeLibrary;
use App\Models\Masters\Images;

class ImagesResource extends JsonResource
{
    public const RESOURCE_KEY_DATA = 'data';
    public const RESOURCE_KEY_TEXT = 'text';
    public const RESOURCE_KEY_VALUE = 'value';


    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // $this->resourceはcollection
        // $this->resource->itemsは検索結果の各レコードをstdClassオブジェクトとして格納した配列
        return [
            self::RESOURCE_KEY_DATA => $this->resource->toArray($request)
        ];
    }

    /**
     * Transform the resource into an array for get roles collection.
     *
     * @param Collection $collection
     * @return array
     */
    public static function toArrayForGetCollection(Collection $collection)
    {
        // レスポンス
        $response = [];

        foreach ($collection as $item) {
            $response[self::RESOURCE_KEY_DATA][] = $item;
        }

        return $response;
    }

    /**
     * Transform the resource into an array for get roles collection.
     *
     * @param Collection $collection
     * @return array
     */
    public static function toArrayForGetFirstByUuid(Collection $collection)
    {
        // レスポンス
        $response = [];

        foreach ($collection as $item) {
            // stdClass $item
            $response = [
                Images::ID         => $item->{Images::ID},
                Images::UUID       => $item->{Images::UUID},
                Images::NAME       => $item->{Images::NAME},
                Images::EXTENTION  => $item->{Images::EXTENTION},
                Images::MIME_TYPE  => $item->{Images::MIME_TYPE},
                Images::S3_KEY     => $item->{Images::S3_KEY},
                Images::VERSION    => $item->{Images::VERSION},
                Images::CREATED_AT =>  $item->{Images::CREATED_AT},
                Images::UPDATED_AT =>  $item->{Images::UPDATED_AT},
            ];
        }

        return $response;
    }

    /**
     * Transform the resource into an array for create.
     *
     * @param array<string, string> $fileReousrce
     * @return array
     */
    public static function toArrayForCreate(array $fileReousrce): array
    {
        $dateTime = TimeLibrary::getCurrentDateTime();

        return [
            Images::UUID       => $fileReousrce[Images::UUID],
            Images::NAME       => $fileReousrce[Images::NAME],
            Images::EXTENTION  => $fileReousrce[Images::EXTENTION],
            Images::MIME_TYPE  => $fileReousrce[Images::MIME_TYPE],
            Images::S3_KEY     => $fileReousrce[Images::S3_KEY],
            Images::VERSION    => TimeLibrary::strToTimeStamp($dateTime),
            Images::CREATED_AT => $dateTime,
            Images::UPDATED_AT => $dateTime,
        ];
    }

    /**
     * Transform the resource into an array for update.
     *
     * @param array<string, string> $fileReousrce
     * @return array
     */
    public static function toArrayForUpdate(array $fileReousrce): array
    {
        $dateTime = TimeLibrary::getCurrentDateTime();

        return [
            Images::UUID       => $fileReousrce[Images::UUID],
            Images::NAME       => $fileReousrce[Images::NAME],
            Images::EXTENTION  => $fileReousrce[Images::EXTENTION],
            Images::MIME_TYPE  => $fileReousrce[Images::MIME_TYPE],
            Images::S3_KEY     => $fileReousrce[Images::S3_KEY],
            Images::VERSION    => TimeLibrary::strToTimeStamp($dateTime),
            Images::UPDATED_AT => $dateTime,
        ];
    }

    /**
     * Transform the resource into an array for delete.
     *
     * @return array
     */
    public static function toArrayForDelete(): array
    {
        $dateTime = TimeLibrary::getCurrentDateTime();

        return [
            Images::VERSION    => TimeLibrary::strToTimeStamp($dateTime),
            Images::UPDATED_AT => $dateTime,
            Images::DELETED_AT => $dateTime,
        ];
    }
}
