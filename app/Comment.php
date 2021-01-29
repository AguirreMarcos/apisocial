<?php

namespace App;

use App\Post;
use App\User;
use App\Heart;
use Illuminate\Database\Eloquent\Model;
use App\Transformers\CommentTransformer;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public $transformer = CommentTransformer::class;

    public function hearts(){
        return $this->morphMany(Heart::class, 'heartable');
    }

    public function post(){
        return $this->belongsTo(Post::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'body',
        'post_id',
        'user_id',
        'visibility',
    ];
}
