<?php

namespace App\Models\Masters;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use App\Library\Array\ArrayLibrary;
use App\Library\Database\ShardingLibrary;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\SoftDeletes;

class OAuthUsers extends Authenticatable implements JWTSubject
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    // カラム一覧
    public const ID = 'id';
    public const NAME = 'name';
    public const EMAIL = 'email';
    public const EMAIL_VERIFIED_AT = 'email_verified_at';
    public const PASSWORD = 'password';
    public const REMEMBER_TOKEN = 'remember_token';
    public const GITT_HUB_ID = 'github_id';
    public const GIT_HUB_TOKEN = 'github_token';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
    public const DELETED_AT = 'deleted_at';

    //テーブル名指定
    protected $table = 'oauth_users';

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
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        self::NAME,
        self::EMAIL,
        self::PASSWORD,
        self::GIT_HUB_TOKEN,
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        self::PASSWORD,
        self::REMEMBER_TOKEN
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        self::EMAIL_VERIFIED_AT => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [];

    public function getUserId()
    {
        return  $this->id;
    }

    public function getUserName()
    {
        return $this->name;
    }

    public function getUserEmail()
    {
        return $this->email;
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.(JWTSubject)
     *
     * @a return mixed
     */
    public function getJWTIdentifier()
    {
        // primary keyを取得
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.(JWTSubject)
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * get query builder by user id.
     *
     * @return Builder
     */
    public function getQueryBuilder(): Builder
    {
        return DB::table($this->getTable());
    }

    /**
     * get single Record record by user id.
     *
     * @param int $userId user id
     * @param bool $isLock exec lock For Update
     * @return array|null
     */
    public function getRecordByUserId(int $userId, bool $isLock = false): array|null
    {
        $query = DB::table($this->getTable())->where(self::ID, '=', $userId);

        if ($isLock) {
            $query->lockForUpdate();
        }

        $record = $query->first();

        if (empty($record)) {
            return null;
        }

        return ArrayLibrary::toArray($record);
    }

    /**
     * get single Record record by user id.
     *
     * @param int $userId user id
     * @param bool $isLock exec lock For Update
     * @return array|null
     */
    public function getRecordByGitHubUserId(int $userId, bool $isLock = false): array|null
    {
        $query = DB::table($this->getTable())->where(self::GITT_HUB_ID, '=', $userId);

        if ($isLock) {
            $query->lockForUpdate();
        }

        $record = $query->first();

        if (empty($record)) {
            return null;
        }

        return ArrayLibrary::toArray($record);
    }

    /**
     * insert record.
     *
     * @param array $resource resource
     * @return bool
     */
    public function insertByUserId(array $resource): bool
    {
        return DB::table($this->getTable())->insert($resource);
    }
}
