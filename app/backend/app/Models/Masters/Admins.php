<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Auth\Passwords\PasswordBrokerManager;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Library\Random\RandomStringLibrary;

class Admins extends Authenticatable implements JWTSubject
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    protected $carbon;
    protected $now;

    // カラム一覧
    public const ID = 'id';
    public const NAME = 'name';
    public const EMAIL = 'email';
    public const EMAIL_VERIFIED_AT = 'email_verified_at';
    public const PASSWORD = 'password';
    public const REMEMBER_TOKEN = 'remember_token';
    public const CRREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
    public const DELETED_AT = 'deleted_at';

    //テーブル名指定
    protected $table = 'admins';

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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        self::NAME,
        self::EMAIL,
        self::PASSWORD,
        self::UPDATED_AT
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [];

    public function __construct()
    {
        $this->carbon = new Carbon();
        $this->now = $this->carbon->now()->timestamp;
    }

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
     * sent password reset mail.
     *
     * @return void
     */
    public function sentPasswordResetMail(): void
    {
        $token = RandomStringLibrary::getRandomStringValue();
        $this->sendPasswordResetNotification($token);
    }
}
