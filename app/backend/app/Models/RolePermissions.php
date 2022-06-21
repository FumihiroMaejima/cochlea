<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Masters\Permissions;
use App\Models\Roles;

class RolePermissions extends Model
{
    use HasFactory;
    use SoftDeletes;

    // カラム一覧
    public const ID = 'id';
    public const NAME = 'name';
    public const SHORT_NAME = 'short_name';
    public const ROLE_ID = 'role_id';
    public const PERMISSION_ID = 'permission_id';
    public const CRREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
    public const DELETED_AT = 'deleted_at';

    //テーブル名指定
    protected $table = 'role_permissions';

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

    /**
    * Define a many-to-many relationship.
    * 設定されているロールの取得
    *
    * @return Roles|null
    */
    public function roles()
    {
        return $this->belongsTo(Roles::class, 'role_id');
        // return $this->belongsToMany(Roles::class, 'role_id');
    }

    /**
    * Define a many-to-many relationship.
    * 設定されている権限の取得
    *
    * @return Permissions|null
    */
    public function permissions()
    {
        return $this->belongsTo(Permissions::class, 'permission_id');
    }
}
