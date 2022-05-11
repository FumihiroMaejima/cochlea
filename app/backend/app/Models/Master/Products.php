<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Products extends Model
{
    use HasFactory;
    use SoftDeletes;

    // カラム一覧
    public const ID                = 'id';
    public const NAME              = 'name';
    public const DETAIL            = 'detail';
    public const TYPE              = 'type';
    public const PRICE             = 'price';
    public const UNIT              = 'unit';
    public const MANUFACTURE       = 'manufacturer';
    public const NOTICE_START_AT   = 'notice_start_at';
    public const NOTICE_END_AT     = 'notice_end_at';
    public const PURCHASE_START_AT = 'purchase_start_at';
    public const PURCHASE_END_AT   = 'purchase_end_at';
    public const IMAGE             = 'image';
    public const CRREATED_AT       = 'created_at';
    public const UPDATED_AT        = 'updated_at';
    public const DELETED_AT        = 'deleted_at';

    //テーブル名指定
    protected $table = 'products';

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

    public function __construct()
    {
    }
}
