<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Library\Array\ArrayLibrary;

class User extends Authenticatable implements JWTSubject
{
    /* use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable; */

    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    public const IS_LEFT_FROM_SERVICE = 1;

    // カラム一覧
    public const ID = 'id';
    public const NAME = 'name';
    public const EMAIL = 'email';
    public const EMAIL_VERIFIED_AT = 'email_verified_at';
    public const PASSWORD = 'password';
    public const ROLE = 'role';
    public const REMEMBER_TOKEN = 'remember_token';
    // public const CURRENT_TEAM_ID = 'current_team_id';
    // public const PROFILE_PHOTO_PATH = 'profile_photo_path';
    public const IS_LEFT = 'is_left';
    public const CODE_VERIFIED_AT = 'code_verified_at';
    public const LAST_LOGIN_AT = 'last_login_at';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
    public const DELETED_AT = 'deleted_at';

    //テーブル名指定
    protected $table = 'users';

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
        self::IS_LEFT,
        self::CODE_VERIFIED_AT,
        self::LAST_LOGIN_AT,
        self::UPDATED_AT,
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
     * get single Record record by user id.
     *
     * @param int $userId user id
     * @param bool $isLock exec lock For Update
     * @return array|null
     */
    public function getRecordByUserId(int $userId, bool $isLock = false): array|null
    {
        $query = DB::table($this->getTable())->where(self::ID, '=', $userId)->where(self::DELETED_AT, '=', null);

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
     * insert record & get record id.
     *
     * @param array $resource resource
     * @return int
     */
    public function insertUserAndGetId(array $resource): int
    {
        return DB::table($this->getTable())->insertGetId($resource);
    }

    /**
     * update code verified at.
     *
     * @param int $userId user id
     * @param string $codeVerifiedAt code verified at
     * @return bool
     */
    public function updateCodeVerifiedAt(int $userId, string $codeVerifiedAt): bool
    {
        $result = DB::table($this->getTable())
            ->where(self::ID, '=', $userId)
            ->where(self::DELETED_AT, '=', null)
            ->update([self::CODE_VERIFIED_AT => $codeVerifiedAt]);

        return $result > 0;
    }

    /**
     * update last login at.
     *
     * @param int $userId user id
     * @param string $lastLoginAt last login at
     * @return bool
     */
    public function updateLastLoginAt(int $userId, string $lastLoginAt): bool
    {
        $result = DB::table($this->getTable())
            ->where(self::ID, '=', $userId)
            ->where(self::DELETED_AT, '=', null)
            ->update([self::LAST_LOGIN_AT => $lastLoginAt]);

        return $result > 0;
    }

    /**
     * update is left at & reset user auth data.
     *
     * @param int $userId user id
     * @param string $dateTime date time
     * @return bool
     */
    public function updateIsLeft(int $userId, string $dateTime): bool
    {
        $result = DB::table($this->getTable())
            ->where(self::ID, '=', $userId)
            ->where(self::DELETED_AT, '=', null)
            ->update(
                [
                    self::EMAIL => null,
                    self::PASSWORD => null,
                    self::IS_LEFT => self::IS_LEFT_FROM_SERVICE,
                    self::DELETED_AT => $dateTime,
                ]
            );

        return $result > 0;
    }
}
