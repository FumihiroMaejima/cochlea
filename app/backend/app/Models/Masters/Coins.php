<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coins extends Model
{
    use HasFactory;
    use SoftDeletes;

    // カラム一覧
    public const ID          = 'id';
    public const NAME        = 'name';
    public const DETAIL      = 'detail';
    public const PRICE       = 'price';
    public const COST        = 'cost';
    public const START_AT    = 'start_at';
    public const END_AT      = 'end_at';
    public const IMAGE       = 'image';
    public const CRREATED_AT = 'created_at';
    public const UPDATED_AT  = 'updated_at';
    public const DELETED_AT  = 'deleted_at';

    //テーブル名指定
    protected $table = 'coins';

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
        self::DETAIL,
        self::PRICE,
        self::COST,
        self::START_AT,
        self::END_AT,
        self::IMAGE,
        self::CRREATED_AT,
        self::UPDATED_AT
    ];

    public function __construct()
    {
    }
}
