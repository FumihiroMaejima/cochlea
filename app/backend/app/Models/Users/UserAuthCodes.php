<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Users\BaseUserDataModel;

class UserAuthCodes extends BaseUserDataModel
{
    use HasFactory;
    use SoftDeletes;

    // カラム一覧
    public const USER_ID = 'user_id';
    public const CODE = 'code';
    public const IS_USED = 'is_used';
    public const START_AT = 'start_at';
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
        self::CODE,
        self::IS_USED,
        self::START_AT,
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
}
