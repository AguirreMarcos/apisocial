<?php

namespace App\Http\Controllers\Post;

use App\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class PostCommentController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Post $post)
    {
        $comments = $post->comments;
        return $this->showAll($comments);
    }
}
