<?php

namespace App\Transformers;

use App\Post;
use League\Fractal\TransformerAbstract;

class PostTransformer extends TransformerAbstract
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
    public function transform(Post $post)
    {
        return [
            'post_identifier' => (int)$post->id,
            'post_name' => (string)$post->title,
            'post_text' => (string)$post->description,
            'post_file' => (string)$post->media,
            'viewed_by' => (int)$post->visibility,
            'user_identifier' => (int)$post->user_id,
            'created_datetime' => (string)$post->created_at,
            'updated_datetime' => (string)$post->updated_at,
            'deleted_datetime' => isset($post->deleted_at) ? (string)$post->deleted_at : null,

            'links' => [
                [
                    'rel' => 'self',
                    'href' => route('posts.show', $post->id),
                ],
                [
                    'rel' => 'post.user',
                    'href' => route('users.show', $post->user_id),
                ],
                [
                    'rel' => 'post.comments',
                    'href' => route('posts.comments.index', $post->id),
                ],
                [
                    'rel' => 'post.hearts',
                    'href' => route('posts.hearts.index', $post->id),
                ],
            ],
        ];
    }

    public static function originalAttribute($index){
        $attributes = [
            'post_identifier' => 'id',
            'post_name' => 'title',
            'post_text' => 'description',
            'post_file' => 'media',
            'viewed_by' => 'visibility',
            'user_identifier' => 'user_id',
            'created_datetime' => 'created_at',
            'updated_datetime' => 'updated_at',
            'deleted_datetime' => 'deleted_at',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

    public static function transformedAttribute($index){
        $attributes = [
            'id' => 'post_identifier',
            'title' => 'post_name',
            'description' => 'post_text',
            'media' => 'post_file',
            'visibility' => 'viewed_by',
            'user_id' => 'user_identifier',
            'created_at' => 'created_datetime',
            'updated_at' => 'updated_datetime',
            'deleted_at' => 'deleted_datetime',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}
