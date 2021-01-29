<?php

namespace App\Http\Controllers\User;

use App\Post;
use App\User;
use App\Heart;
use Illuminate\Http\Request;
use App\Traits\UserFunctions;
use App\Transformers\HeartTransformer;
use App\Http\Controllers\ApiController;

class UserPostHeartController extends ApiController
{

    use UserFunctions;

    public function __construct(){
        parent::__construct();
        $this->middleware('transform.input:' . HeartTransformer::class)->only('store');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(User $user, Post $post)
    {
        // me falta comprobar la visibilidad del usuario con el post
        if(!$this->canReach($user, $post)){
            return $this->errorResponse('User does not have available visibility for this content', 403);
        }
        if($this->isHeartedByUser($user, $post)){
            return $this->errorResponse('User already liked this content', 422);
        }

        $heart = factory(Heart::class)->make([
            'user_id' => $user->id,
        ]);
        $post->hearts()->save($heart);

        return $this->showOne($heart, 201);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user, Post $post, Heart $heart = null)
    {
        if(!$this->canReach($user, $post)){
            return $this->errorResponse('User does not have available visibility for this content', 403);
        }

        $heartUserPost = $post->hearts()->where('user_id', $user->id);
        if($heartUserPost->count() < 1){
            return $this->errorResponse('User must like this content before unliked it', 422);
        }
        $heartToDelete = $heartUserPost->first();
        $heartToDelete->delete();
        return $this->showOne($heartToDelete);
    }

}
