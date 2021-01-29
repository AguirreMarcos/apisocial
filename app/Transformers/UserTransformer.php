<?php

namespace App\Transformers;

use App\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
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
    public function transform(User $user)
    {
        return [
            'user_identifier' => (int)$user->id,
            'user_name' => (string)$user->name,
            'user_nickname' => (string)$user->nickname,
            'user_mail' => (string)$user->email,
            'user_avatar' => (string)$user->profile_img_path,
            'is_verified' => (int)$user->verified,
            'is_admin' => ($user->admin === 'true'),
            'created_datetime' => (string)$user->created_at,
            'updated_datetime' => (string)$user->updated_at,
            'deleted_datetime' => isset($user->deleted_at) ? (string)$user->deleted_at : null,

            'links' => [
                [
                    'rel' => 'self',
                    'href' => route('users.show', $user->id),
                ],
                [
                    'rel' => 'user.posts',
                    'href' => route('users.posts.index', $user->id),
                ],
                [
                    'rel' => 'user.comments',
                    'href' => route('users.comments.index', $user->id),
                ],
                [
                    'rel' => 'user.hearts',
                    'href' => route('users.hearts.index', $user->id),
                ],
                [
                    'rel' => 'user.friends',
                    'href' => route('users.friends.index', $user->id),
                ],
                [
                    'rel' => 'user.invitations.sender',
                    'href' => route('users.friends.sender', $user->id),
                ],
                [
                    'rel' => 'user.invitations.receiver',
                    'href' => route('users.friends.receiver', $user->id),
                ],

            ],
        ];
    }

    public static function originalAttribute($index){
        $attributes = [
            'user_identifier' => 'id',
            'user_name' => 'name',
            'user_nickname' => 'nickname',
            'user_mail' => 'email',
            'user_avatar' => 'profile_img_path',
            'is_verified' => 'verified',
            'is_admin' => 'admin',
            'created_datetime' => 'created_at',
            'updated_datetime' => 'updated_at',
            'deleted_datetime' => 'deleted_at',
            'password' => 'password',
            'password_confirmation' => 'password_confirmation',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

    public static function transformedAttribute($index){
        $attributes = [
             'id' => 'user_identifier',
             'name' => 'user_name',
             'nickname' => 'user_nickname',
             'email' => 'user_mail',
             'profile_img_path' => 'user_avatar',
             'verified' => 'is_verified',
             'admin' => 'is_admin',
             'created_at' => 'created_datetime',
             'updated_at' => 'updated_datetime',
             'deleted_at' => 'deleted_datetime',
             'password' => 'password',
             'password_confirmation' => 'password_confirmation',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

}
