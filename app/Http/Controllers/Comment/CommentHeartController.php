<?php

namespace App\Http\Controllers\Comment;

use App\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class CommentHeartController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Comment $comment)
    {
        $hearts = $comment->hearts;
        return $this->showAll($hearts);
    }
}
