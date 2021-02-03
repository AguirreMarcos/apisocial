<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UserTest extends TestCase
{
    use DatabaseMigrations, WithFaker;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    //a user can be created
    public function testUserCanBeCreated(){
        $this->withoutExceptionHandling();
        User::flushEventListeners();
        /* $user = factory(User::class)
            ->states('transformed')
            ->raw();
        dd($user); */
        $user = [
            'user_name' => $this->faker->name(),
            'user_nickname' => $this->faker->userName(),
            'user_mail' => $this->faker->unique()->safeEmail(),
            'password' => 'secret',
            'password_confirmation' => 'secret',
            'user_avatar' => $this->faker->randomElement(['1.jpg', '2.jpg', '3.jpg', '4.jpg', '5.jpg', '6.jpg', '7.jpg', '8.jpg']),
        ];
        //dd($user);
        $this->post('/users', $user)
            ->assertStatus(201);
    }
}
