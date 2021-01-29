<?php

namespace App\Http\Controllers\User;

use App\Post;
use App\User;
use Illuminate\Http\Request;
use App\Traits\UserFunctions;
use Illuminate\Validation\Rule;
use App\Transformers\PostTransformer;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserPostController extends ApiController
{

    use UserFunctions;

    public function __construct(){
        parent::__construct();
        $this->middleware('transform.input:' . PostTransformer::class)->only('store', 'update');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(User $user)
    {
        $posts = $user->posts;
        return $this->showAll($posts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $user)
    {

        $rules = [
            'title' => 'required',
            'visibility' => Rule::in([Post::JUST_FOR_ME, Post::JUST_FOR_FRIENDS, Post::FRIENDS_OF_FRIENDS, Post::FOR_EVERYONE]),
            'media' => 'file|mimes:jpeg,jpg,png,gif,mp3,mp4,avi,mov,mkv,webm',
            //'media_type' => Rule::in([Post::VIDEO_TYPE, Post::IMAGE_TYPE]),
        ];

        $this->validate($request, $rules);


        $data = $request->all();

        if($request->has('media')){
            $data['media'] = $request->media->store('', 'media_driver');
        }

        if(!$request->has('visibility')){
            $data['visibility'] = Post::JUST_FOR_FRIENDS;
        }

        $data['user_id'] = $user->id;

        $post = Post::create($data);

        return $this->showOne($post, 201);


    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user, Post $post)
    {
        $rules = [
            'visibility' => Rule::in([Post::JUST_FOR_ME, Post::JUST_FOR_FRIENDS, Post::FRIENDS_OF_FRIENDS, Post::FOR_EVERYONE]),
            'media' => 'file|mimes:jpeg,jpg,png,gif,mp3,mp4,avi,mov,mkv,webm',
            //'media_type' => Rule::in([Post::VIDEO_TYPE, Post::IMAGE_TYPE]),
        ];

        $this->validate($request, $rules);

        $this->verifyUser($user, $post);

        if($request->hasFile('media')){
            Storage::disk('media_driver')->delete($post->media);
            $post->media = $request->media->store('', 'media_driver');
        } //important to do before fill the post

        $post->fill($request->only([
            'title',
            'description',
            'media',
            'visibility',
        ]));

        if($post->isClean()){
            return $this->errorResponse('At least one parameter must be provided to update this content', 422);
        }

        $post->save();

        return $this->showOne($post);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user, Post $post)
    {
        $this->verifyUser($user, $post);

        Storage::disk('media_driver')->delete($post->media);

        $post->delete();

        return $this->showOne($post);
    }

}
