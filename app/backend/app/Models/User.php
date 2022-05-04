<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

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
    protected $carbon;
    protected $now;

    // カラム一覧
    public const ID = 'id';
    public const NAME = 'name';
    public const MESSAGE = 'email';
    public const EMAIL_VERIFIED_AT = 'email_verified_at';
    public const PASSWORD = 'password';
    public const ROLE = 'role';
    public const REMEMBER_TOKEN = 'remember_token';
    public const CURRENT_TEAM_ID = 'current_team_id';
    public const PROFILE_PHOTO_PATH = 'profile_photo_path';
    public const CRREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
    // public const DELETED_AT = 'deleted_at';

    //テーブル名指定
    protected $table = 'users';

    /**
     * used in initializeSoftDeletes()
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token'
    ];

    /**
     * The attributes that should be cast.
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
}
