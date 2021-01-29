<?php

namespace App;

use App\User;
use App\Heart;
use App\Comment;
use App\Transformers\PostTransformer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;

    public $transformer = PostTransformer::class;

    protected $dates = ['deleted_at'];
    /**
     * Const values
     */
    const JUST_FOR_ME = 0;
    const JUST_FOR_FRIENDS = 1;
    const FRIENDS_OF_FRIENDS = 2;
    const FOR_EVERYONE = 3;
    const VIDEO_TYPE = 'video';
    const IMAGE_TYPE = 'image';

    public function comments(){
        return $this->hasMany(Comment::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function hearts(){
        return $this->morphMany(Heart::class, 'heartable');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'media',
        'user_id',
        'visibility',
        //'media_type',
    ];

}
