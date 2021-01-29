<?php

namespace App\Http\Controllers\Post;

use App\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class PostHeartController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Post $post)
    {
        $hearts = $post->hearts;
        return $this->showAll($hearts);
    }


}
