<?php

namespace App\Transformers;

use App\Heart;
use League\Fractal\TransformerAbstract;

class HeartTransformer extends TransformerAbstract
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
    public function transform(Heart $heart)
    {
        return [
            'heart_identifier' => (int)$heart->id,
            'user_identifier' => (int)$heart->user_id,
            'hearted_identifier' => (int)$heart->heartable_id,
            'hearted_type' => (string)$heart->heartable_type,
            'updated_datetime' => (string)$heart->created_at,
            'created_datetime' => (string)$heart->updated_at,

            'links' => [
                [
                    'rel' => 'self',
                    'href' => route('hearts.show', $heart->id),
                ],
                [
                    'rel' => 'heart.user',
                    'href' => route('users.show', $heart->user_id),
                ],
                [
                    'rel' => 'heart.content',
                    'href' => route('hearts.content', $heart->id),
                ],
            ],
        ];
    }

    public static function originalAttribute($index){
        $attributes = [
            'heart_identifier' => 'id',
            'user_identifier' => 'user_id',
            'hearted_identifier' => 'heartable_id',
            'hearted_type' => 'heartable_type',
            'created_datetime' => 'created_at',
            'updated_datetime' => 'updated_at',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

    public static function transformedAttribute($index){
        $attributes = [
            'id' => 'heart_identifier',
            'user_id' => 'user_identifier',
            'heartable_id' => 'hearted_identifier',
            'heartable_type' => 'hearted_type',
            'created_at' => 'created_datetime',
            'updated_at' => 'updated_datetime',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}
