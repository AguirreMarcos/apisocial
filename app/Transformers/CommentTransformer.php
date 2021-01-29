<?php

namespace App\Transformers;

use App\Comment;
use League\Fractal\TransformerAbstract;

class CommentTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        //
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        //
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Comment $comment)
    {
        return [
            'comment_identifier' => (int)$comment->id,
            'comment_content' => (string)$comment->body,
            'post_identifier' => (int)$comment->post_id,
            'user_identifier' => (int)$comment->user_id,
            'viewed_by' => (int)$comment->visibility,
            'created_datetime' => (string)$comment->created_at,
            'updated_datetime' => (string)$comment->updated_at,
            'deleted_datetime' => isset($comment->deleted_at) ? (string)$comment->deleted_at : null,

            'links' => [
                [
                    'rel' => 'self',
                    'href' => route('comments.show', $comment->id),
                ],
                [
                    'rel' => 'comment.user',
                    'href' => route('users.show', $comment->user_id),
                ],
                [
                    'rel' => 'comment.post',
                    'href' => route('posts.show', $comment->post_id),
                ],
                [
                    'rel' => 'comment.hearts',
                    'href' => route('comments.hearts.index', $comment->id),
                ],
            ],
        ];
    }
    public static function originalAttribute($index){
        $attributes = [
            'comment_identifier' => 'id',
            'comment_content' => 'body',
            'post_identifier' => 'post_id',
            'user_identifier' => 'user_id',
            'viewed_by' => 'visibility',
            'created_datetime' => 'created_at',
            'updated_datetime' => 'updated_at',
            'deleted_datetime' => 'deleted_at',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

    public static function transformedAttribute($index){
        $attributes = [
            'id' => 'comment_identifier',
            'body' => 'comment_content',
            'post_id' => 'post_identifier',
            'user_id' => 'user_identifier',
            'visibility' => 'viewed_by',
            'created_at' => 'created_datetime',
            'updated_at' => 'updated_datetime',
            'deleted_at' => 'deleted_datetime',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

}
