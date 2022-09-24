<?php

namespace App\Models\Logs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Logs\BaseLogDataModel;

class UserReadInformationLog extends BaseLogDataModel
{
    use HasFactory;
    use SoftDeletes;

    // カラム一覧
    public const USER_ID = 'user_id';
    public const INFORMATION_ID = 'information_id';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
    public const DELETED_AT = 'deleted_at';

    //テーブル名指定
    protected $table = 'user_read_information_log';

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
        self::INFORMATION_ID,
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
        self::INFORMATION_ID,
        self::UPDATED_AT,
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];
}
