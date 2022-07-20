<?php

namespace App\Models\Logs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Logs\BaseLogDataModel;

class UserCoinPaymentLog extends BaseLogDataModel
{
    use HasFactory;
    use SoftDeletes;

    // 決済のステータス
    public const PAYMENT_STATUS_START = 1; // 決済開始
    public const PAYMENT_STATUS_WAITING_FOR_DEPOSIT = 2; // 決済中(入金待ち)
    public const PAYMENT_STATUS_COMPLETE = 3; // 決済完了
    public const PAYMENT_STATUS_EXPIRED = 98; // 期限切れ
    public const PAYMENT_STATUS_CANCEL = 99; // キャンセル

    public const PAYMENT_STATUS_LIST = [
        self::PAYMENT_STATUS_START => '決済開始',
        self::PAYMENT_STATUS_WAITING_FOR_DEPOSIT => '決済中(入金待ち)',
        self::PAYMENT_STATUS_COMPLETE => '決済完了',
        self::PAYMENT_STATUS_EXPIRED => '期限切れ',
        self::PAYMENT_STATUS_CANCEL => 'キャンセル',
    ];

    // カラム一覧
    public const USER_ID = 'user_id';
    public const ORDER_ID = 'order_id';
    public const COIN_ID = 'coin_id';
    public const STATUS = 'status';
    public const CRREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
    public const DELETED_AT = 'deleted_at';

    //テーブル名指定
    protected $table = 'user_coin_payment_log';

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
        self::ORDER_ID,
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
        self::ORDER_ID,
        self::COIN_ID,
        self::STATUS,
        self::UPDATED_AT,
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];
}
