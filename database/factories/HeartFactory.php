<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Post;
use App\User;
use App\Heart;
use App\Comment;
use Faker\Generator as Faker;

$factory->define(Heart::class, function (Faker $faker) {
    return [
    ];
});

$factory->state(Heart::class, 'comment', function (Faker $faker) {
    return [
    ];
});
