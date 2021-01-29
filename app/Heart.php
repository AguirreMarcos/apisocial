<?php

namespace App;

use App\User;
use App\Transformers\HeartTransformer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Heart extends Model
{
    //use SoftDeletes;

    //protected $dates = ['deleted_at'];

    public $transformer = HeartTransformer::class;

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function heartable(){
        return $this->morphTo();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
    ];
}
