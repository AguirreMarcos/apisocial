<?php

namespace App;

use App\Post;
use App\User;
use App\Heart;
use App\Comment;
use Illuminate\Support\Str;
use Laravel\Passport\HasApiTokens;
use App\Transformers\UserTransformer;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes, HasApiTokens;

    public $transformer = UserTransformer::class;

    protected $dates = ['deleted_at'];

    /**
     * const values
     */
    const USER_VERIFIED = '1';
    const USER_NOT_VERIFIED = '0';

    const ADMIN_USER = 'true';
    const REGULAR_USER = 'false';

    const FRIENDSHIP_PENDING = 'false';
    const FRIENDSHIP_ACCEPTED = 'true';

    const FRIENDSHIP_RECEIVER = 'false';
    const FRIENDSHIP_SENDER = 'true';

    public function posts(){
        return $this->hasMany(Post::class);
    }

    public function hearts(){
        return $this->hasMany(Heart::class);
    }

    public function comments(){
        return $this->hasMany(Comment::class);
    }

    //ManyToMany relation for users and friends
    public function users(){
        return $this->belongsToMany(User::class, 'user_user', 'friend_id', 'self_id')
            ->withPivot('status', 'is_sender')
            ->withTimeStamps();
    }

    public function friends(){
        return $this->belongsToMany(User::class, 'user_user', 'self_id', 'friend_id')
            ->withPivot('status', 'is_sender')
            ->withTimeStamps();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'nickname',
        'email',
        'password',
        'profile_img_path',
        'verified',
        'verification_token',
        'admin',
    ];

    /**
     * mutator for name
     */
    public function setNameAttribute($value){
        $this->attributes['name'] = strtolower($value);
    }

    /**
     * mutator for email
     */
    public function setEmailAttribute($value){
        $this->attributes['email'] = strtolower($value);
    }

    /**
     * Accessor for name
     */
    public function getNameAttribute($value){
        return ucwords($value);
    }


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'verification_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isVerified(){
        return $this->verified == User::USER_VERIFIED;
    }

    public function isAdmin(){
        return $this->admin == User::ADMIN_USER;
    }

    public static function generateVerificationToken(){
        /* return str_random(40); */ // Eliminados a partir de laravel 6, en su lugar usar Str o Arr classes:
        return Str::random(40);
    }

}
