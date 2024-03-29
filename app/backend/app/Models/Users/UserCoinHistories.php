<?php

declare(strict_types=1);

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Users\BaseUserDataModel;
use App\Library\Array\ArrayLibrary;
use App\Library\Time\TimeLibrary;

class UserCoinHistories extends BaseUserDataModel
{
    use HasFactory;
    use SoftDeletes;

    // デフォルトコイン数
    public const DEFAULT_COIN_COUNT = 0;

    // 履歴の種類
    public const USER_COINS_HISTORY_TYPE_PURCHASED = 1; // 購入
    public const USER_COINS_HISTORY_TYPE_GAIN = 2; // 獲得
    public const USER_COINS_HISTORY_TYPE_CONSUME = 3; // 消費
    public const USER_COINS_HISTORY_TYPE_EXPIRED = 4; // 期限切れ
    public const USER_COINS_HISTORY_TYPE_COMPENSATION = 5; // 補填

    // 履歴の種類(文字列)
    public const USER_COINS_HISTORY_TYPE_STRING_PURCHASED = '購入';
    public const USER_COINS_HISTORY_TYPE_STRING_GAIN = '獲得';
    public const USER_COINS_HISTORY_TYPE_STRING_CONSUME = '消費';
    public const USER_COINS_HISTORY_TYPE_STRING_EXPIRED = '期限切れ';
    public const USER_COINS_HISTORY_TYPE_STRING_COMPENSATION = '補填';

    public const USER_COINS_HISTORY_TYPE_VALUES = [
        self::USER_COINS_HISTORY_TYPE_PURCHASED,
        self::USER_COINS_HISTORY_TYPE_GAIN,
        self::USER_COINS_HISTORY_TYPE_CONSUME,
        self::USER_COINS_HISTORY_TYPE_EXPIRED,
        self::USER_COINS_HISTORY_TYPE_COMPENSATION,
    ];

    public const USER_COINS_HISTORY_TYPE_VALUE_LIST = [
        self::USER_COINS_HISTORY_TYPE_PURCHASED => self::USER_COINS_HISTORY_TYPE_STRING_PURCHASED,
        self::USER_COINS_HISTORY_TYPE_GAIN => self::USER_COINS_HISTORY_TYPE_STRING_GAIN,
        self::USER_COINS_HISTORY_TYPE_CONSUME => self::USER_COINS_HISTORY_TYPE_STRING_CONSUME,
        self::USER_COINS_HISTORY_TYPE_EXPIRED => self::USER_COINS_HISTORY_TYPE_STRING_EXPIRED,
        self::USER_COINS_HISTORY_TYPE_COMPENSATION => self::USER_COINS_HISTORY_TYPE_STRING_COMPENSATION,
    ];

    // カラム一覧
    public const USER_ID = 'user_id';
    public const UUID = 'uuid';
    public const TYPE = 'type';
    public const GET_FREE_COINS = 'get_free_coins';
    public const GET_PAID_COINS = 'get_paid_coins';
    public const GET_LIMITED_TIME_COINS = 'get_limited_time_coins';
    public const USED_FREE_COINS = 'used_free_coins';
    public const USED_PAID_COINS = 'used_paid_coins';
    public const USED_LIMITED_TIME_COINS = 'used_limited_time_coins';
    public const EXPIRED_LIMITED_TIME_COINS = 'expired_limited_time_coins';
    public const EXPIRED_AT = 'expired_at';
    public const OEDER_ID = 'order_id';
    public const PRODUCT_ID = 'product_id';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
    public const DELETED_AT = 'deleted_at';

    //テーブル名指定
    protected $table = 'user_coin_histories';

    // カラムの自動更新をEloquentに許可
    public $timestamps = true;

    // ソフトデリートの有効化(日付へキャストする属性)
    protected $casts = [self::DELETED_AT => 'datetime'];

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = [
        self::USER_ID,
        self::CREATED_AT,
    ];

    /**
     * Indicates if the IDs are auto-incrementing.
     * (primary keyが複数(配列)の場合はfalseを指定する。)
     *
     * @var bool
     */
    public $incrementing = false;

    // 更新可能なカラムリスト
    protected $fillable = [
        self::USER_ID,
        self::UUID,
        self::TYPE,
        self::GET_FREE_COINS,
        self::GET_PAID_COINS,
        self::GET_LIMITED_TIME_COINS,
        self::USED_FREE_COINS,
        self::USED_PAID_COINS,
        self::USED_LIMITED_TIME_COINS,
        self::EXPIRED_LIMITED_TIME_COINS,
        self::EXPIRED_AT,
        self::OEDER_ID,
        self::PRODUCT_ID,
        self::UPDATED_AT,
        self::DELETED_AT,
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * get all records by gain type of expire at & start date of expire
     *
     * @param string $connection connection
     * @param int $shardId shard id
     * @param string $expiredAt expire at
     * @return array<int, array>
     */
    public function getAllByConnectionAndShardIdAndGainAndExpireAt(
        string $connection,
        int $shardId,
        string $expiredAt
    ): array {
        $startAt = TimeLibrary::format($expiredAt, TimeLibrary::DATE_TIME_FORMAT_START_DATE);

        $records = DB::connection($connection)
            ->table($this->getTable() . $shardId)
            // ->where(self::TYPE, self::USER_COINS_HISTORY_TYPE_GAIN)
            ->whereIn(self::TYPE, [self::USER_COINS_HISTORY_TYPE_GAIN, self::USER_COINS_HISTORY_TYPE_COMPENSATION])
            ->whereBetween(self::EXPIRED_AT, [$startAt, $expiredAt])
            ->get()
            ->toArray();

        return ArrayLibrary::toArray($records);
    }

    /**
     * update expired coin record.
     *
     * @param string $connection connection
     * @param int $shardId shard id
     * @param array $resource values
     * @return array<int, array>
     */
    public function insertExpiredCoinRecords(
        string $connection,
        int $shardId,
        array $resource
    ): bool {
        $result = DB::connection($connection)
            ->table($this->getTable() . $shardId)
            ->insert($resource);

        return $result;
    }
}
