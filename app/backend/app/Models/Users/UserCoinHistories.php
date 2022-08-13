<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Users\BaseUserDataModel;

class UserCoinHistories extends BaseUserDataModel
{
    use HasFactory;
    use SoftDeletes;

    // デフォルトコイン数
    public const DEFAULT_COIN_COUNT = 0;

    // カラム一覧
    public const USER_ID = 'user_id';
    public const TYPE = 'type';
    public const GET_FREE_COINS = 'get_free_coins';
    public const GET_PAID_COINS = 'get_paid_coins';
    public const GET_LIMITED_TIME_COINS = 'get_limited_time_coins';
    public const USED_FREE_COINS = 'used_free_coins';
    public const USED_PAID_COINS = 'used_paid_coins';
    public const USED_LIMITED_TIME_COINS = 'used_limited_time_coins';
    public const EXPIRED_LIMITED_TIME_COINS = 'exipired_limited_time_coins';
    public const EXPIRED_AT = 'exipired_at';
    public const OEDER_ID = 'order_id';
    public const PRODUCT_ID = 'product_id';
    public const CRREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
    public const DELETED_AT = 'deleted_at';

    //テーブル名指定
    protected $table = 'user_coin_histories';

    // カラムの自動更新をEloquentに許可
    public $timestamps = true;

    // ソフトデリートの有効化(日付へキャストする属性)
    protected $dates = [self::DELETED_AT];

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = [
        self::USER_ID,
        self::CRREATED_AT,
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
}