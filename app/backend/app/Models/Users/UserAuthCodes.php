<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Library\Time\TimeLibrary;
use App\Models\Users\BaseUserDataModel;

class UserAuthCodes extends BaseUserDataModel
{
    use HasFactory;
    use SoftDeletes;

    public const TYPE_REGISTER = 1; // 新規登録
    public const TYPE_PASSWORD_UPDATE = 2; // パスワード変更
    public const TYPE_PASSWORD_FORGET = 3; // パスワードリセット
    public const TYPE_LINKAGE_REGISTER = 4; // 外部連携
    public const TYPE_LINKAGE_CHANGE = 5; // 外部連携変更

    // カラム一覧
    public const USER_ID = 'user_id';
    public const TYPE = 'type';
    public const CODE = 'code';
    public const COUNT = 'count';
    public const IS_USED = 'is_used';
    public const EXPIRED_AT = 'expired_at';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    //テーブル名指定
    protected $table = 'user_auth_codes';

    // カラムの自動更新をEloquentに許可
    public $timestamps = true;

    // ソフトデリートの有効化(日付へキャストする属性)
    // protected $dates = [];

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = [
        self::USER_ID,
        self::CODE,
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
        self::CODE,
        self::COUNT,
        self::IS_USED,
        self::EXPIRED_AT,
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
     * sort by created at.
     *
     * @param array $records record list
     * @return array
     */
    public static function sortByCreatedAt(array $records): array
    {
        $createdAtList = [];
        foreach ($records as $record) {
            $createdAtList = TimeLibrary::strToTimeStamp($record[self::CREATED_AT]);
        }

        array_multisort($createdAtList, SORT_ASC, $userAuthCodeList);
        return $records;
    }
}
