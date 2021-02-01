<?php

namespace App\Http\Controllers\Heart;

use App\Post;
use App\Heart;
use App\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class HeartController extends ApiController
{
    public function __construct(){
        parent::__construct();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $hearts = Heart::all();
        return $this->showAll($hearts);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Heart  $heart
     * @return \Illuminate\Http\Response
     */
    public function show(Heart $heart)
    {
        return $this->showOne($heart);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Heart  $heart
     * @return \Illuminate\Http\Response
     */
    public function destroy(Heart $heart)
    {
        $heart->delete();
        return $this->showOne($heart);
    }

    public function getContentHearted(Heart $heart){

        if($heart->heartable_type === 'App\Post'){
            return $this->showOne(Post::findOrFail($heart->heartable_id));
        }

        if($heart->heartable_type === 'App\Comment'){
            return $this->showOne(Comment::findOrFail($heart->heartable_id));
        }
    }
}
