<?php

namespace App\Http\Controllers\User;

use App\Post;
use App\User;
use App\Comment;
use Illuminate\Http\Request;
use App\Traits\UserFunctions;
use App\Http\Controllers\ApiController;
use App\Transformers\CommentTransformer;

class UserPostCommentController extends ApiController
{
    use UserFunctions;

    public function __construct(){
        parent::__construct();
        $this->middleware('transform.input:' . CommentTransformer::class)->only('store', 'update');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(User $user, Post $post)
    {
        $comments = $post->comments->where('user_id', $user->id);
        return $this->showAll($comments);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $user, Post $post)
    {
        if(!$this->canReach($user, $post)){
            return $this->errorResponse('User does not have available visibility for this content', 403);
        }

        $rules = [
            'body' => 'required',
        ];

        $this->validate($request, $rules);

        $data = $request->all();

        $data['user_id'] = $user->id;
        $data['post_id'] = $post->id;
        $data['visibility'] = $post->visibility;

        $comment = Comment::create($data);

        return $this->showOne($comment, 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user, Post $post, Comment $comment)
    {
        //no rules needed for comment update

        if(!$this->canReach($user, $post)){
            return $this->errorResponse('User does not have available visibility for this content', 403);
        }

        $this->verifyUser($user, $comment);

        $comment->fill($request->only([
            'body',
        ]));

        if($comment->isClean()){
            return $this->errorResponse('At least one parameter must have changes to update this content', 422);
        }

        $comment->save();

        return $this->showOne($comment);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user, Post $post, Comment $comment)
    {
        if(!$this->canReach($user, $post)){
            return $this->errorResponse('User does not have available visibility for this content', 403);
        }

        $this->verifyUser($user, $comment);

        $comment->delete();
        return $this->showOne($comment);

    }
}
