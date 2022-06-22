<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserComments extends Model
{
    use HasFactory;
    use SoftDeletes;

    // カラム一覧
    public const ID = 'id';
    public const USER_ID = 'user_id';
    public const COMMENT = 'comment';
    public const CRREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
    public const DELETED_AT = 'deleted_at';

    /**
     * The connection name for the model.
     *
     * @var string|null
     */
    protected $connection = 'mysql_user1';

    //テーブル名指定
    protected $table = 'user_comments';

    // カラムの自動更新をEloquentに許可
    public $timestamps = true;

    // ソフトデリートの有効化(日付へキャストする属性)
    protected $dates = [self::DELETED_AT];

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = self::ID;

    // 更新可能なカラムリスト
    protected $fillable = [
        self::UPDATED_AT
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];
}
