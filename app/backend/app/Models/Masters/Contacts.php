<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contacts extends Model
{
    use HasFactory;
    use SoftDeletes;

    // 問合せ
    public const TYPE_QUESTION = 1; // 質問
    public const TYPE_REQUST = 2; // 要望
    public const TYPE_FAILURE = 3; // 障害報告
    public const TYPE_ETC = 99; // その他

    // カラム一覧
    public const ID             = 'id';
    public const NAME           = 'name';
    public const USER_ID        = 'user_id';
    public const TYPE           = 'type';
    public const DETAIL         = 'detail';
    public const FAILURE_DETAIL = 'failure_detail';
    public const FAILURE_AT     = 'failure_at';
    public const CREATED_AT     = 'created_at';
    public const UPDATED_AT     = 'updated_at';
    public const DELETED_AT     = 'deleted_at';

    //テーブル名指定
    protected $table = 'contacts';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * used in initializeSoftDeletes()
     *
     * @var array
     */
    protected $dates = [self::DELETED_AT];

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = self::ID;

    /**
     * The attributes that are mass assignable(複数代入可能な属性(カラム)).
     *
     * @var array
     */
    protected $fillable = [
        self::NAME,
        self::USER_ID,
        self::TYPE,
        self::DETAIL,
        self::FAILURE_DETAIL,
        self::FAILURE_AT,
        self::CREATED_AT,
        self::UPDATED_AT
    ];

    public function __construct()
    {
    }
}
