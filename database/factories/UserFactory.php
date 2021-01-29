<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {

    static $password;

    return [
        'name' => $faker->name,
        'nickname' => $faker->userName,
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        /* 'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password */
        'password' => $password ?: bcrypt('secret'),
        'remember_token' => Str::random(10),
        'verified' => $verified = $faker->randomElement([User::USER_VERIFIED, User::USER_NOT_VERIFIED]),
        'verification_token' => $verified == User::USER_VERIFIED ? null : User::generateVerificationToken(),
        'admin' => $faker->randomElement([User::ADMIN_USER, User::REGULAR_USER]),
        'profile_img_path' => $faker->randomElement(['1.jpg', '2.jpg', '3.jpg', '4.jpg', '5.jpg', '6.jpg', '7.jpg', '8.jpg']),
    ];
});

