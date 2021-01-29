<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Post;
use App\User;
use Faker\Generator as Faker;

$factory->define(Post::class, function (Faker $faker) {

    $image_array = ['9.jpg', '10.jpg', '11.jpg', '12.jpg', '13.jpg', '14.jpg', '15.jpg', '16.jpg',
    '17.jpg', '18.jpg', '19.jpg', '20.jpg', '21.jpg', '22.jpg', '23.jpg', '24.jpg', '25.jpg',
    '26.jpg', '27.jpg', '28.jpg'];

    $video_array = ['1.webm', '2.webm', '3.webm', '4.webm', '5.webm'];

    $type = $faker->randomElement([Post::VIDEO_TYPE, Post::IMAGE_TYPE]);

    return [
        'title' => $faker->sentence,
        'description' => $faker->paragraph(1),
        //'media_type' => $type = $faker->randomElement([Post::VIDEO_TYPE, Post::IMAGE_TYPE]),
        'media' => $type == Post::VIDEO_TYPE ? $faker->randomElement($video_array) :
                            $faker->randomElement($image_array),
        'user_id' => User::all()->random(),
        'visibility' => $faker->randomElement([Post::JUST_FOR_ME, Post::JUST_FOR_FRIENDS,
                        Post::FRIENDS_OF_FRIENDS, Post::FOR_EVERYONE]),
    ];
});
