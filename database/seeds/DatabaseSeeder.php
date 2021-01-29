<?php

use App\Post;
use App\User;
use App\Heart;
use App\Comment;
use Illuminate\Support\Arr;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        User::truncate();
        Post::truncate();
        Comment::truncate();
        Heart::truncate();
        DB::table('user_user')->truncate();

        User::flushEventListeners();
        Post::flushEventListeners();
        Comment::flushEventListeners();
        Heart::flushEventListeners();

        $number_of_users = 30;
        $number_of_posts = 100;
        $number_of_comments = 10;
        $number_of_friends = 8;

        factory(User::class, $number_of_users)->create()
            ->each(function ($user) use ($number_of_friends){

                $friends = User::all();
                $friends = $friends->except($user->id);
                $friends = $friends->random(mt_rand(0,$number_of_friends))
                    ->pluck('id');
                $status = $this->getRandomElement([User::FRIENDSHIP_PENDING, User::FRIENDSHIP_ACCEPTED]);
                /* $user->friends()->attach($friends, array('status' => $status));
                $user->users()->attach($friends, array('status' => $status)); */

                $user->friends()->syncWithoutDetaching($friends);
                $user->users()->syncWithoutDetaching($friends);
                $user->friends()->updateExistingPivot($friends, ['status' => $status, 'is_sender' => User::FRIENDSHIP_SENDER]);
                $user->users()->updateExistingPivot($friends, ['status' => $status, 'is_sender' => User::FRIENDSHIP_RECEIVER]);
            }
        );
        //DB::table('user_user')->update(['status' => $this->getRandomElement([User::FRIENDSHIP_PENDING, User::FRIENDSHIP_ACCEPTED])]);
        factory(Post::class, $number_of_posts)->create()
            ->each(function ($post) use ($number_of_comments){

                $possibleFriends = $this->getPossibleFriends($post);

                $comments = factory(Comment::class,mt_rand(0, $number_of_comments))->make([ //aqui es make para que el guardado se haga al final tras el random
                    'post_id' => $post->id,
                    'visibility' => $post->visibility,
                ]);
                $comments->each(function ($comment) use ($possibleFriends){

                    $possibleFriendsForComments = clone $possibleFriends; //creamos una copia del objeto para hacer el pop
                    $comment->user_id = $possibleFriends->random()->id;

                    $comment->save();
                    $hearts = factory(Heart::class, mt_rand(0, $possibleFriendsForComments->count()))
                        ->states('comment')
                        ->make();
                        //dd($hearts);
                    $hearts->each(function ($heart) use ($possibleFriendsForComments){
                        // en este caso debemos evitar que se repita el usuario ya que un usuario no debe dar like mas de una vez al mismo post
                        $possibleFriend = $possibleFriendsForComments->pop();
                        $heart->user_id = $possibleFriend->id;
                    });
                    if($hearts->isNotEmpty()){
                        $comment->hearts()->saveMany($hearts);
                    }
                });
                //$comments = $comments->random(mt_rand(0,5));
                $post->comments()->saveMany($comments); //cuando es hasMany se utiliza saveMany en lugar de attach que es para belongsToMany

                $hearts = factory(Heart::class, mt_rand(0, $possibleFriends->count()))->make();
                $hearts->each(function ($heart) use ($possibleFriends){
                    // en este caso debemos evitar que se repita el usuario ya que un usuario no debe dar like mas de una vez al mismo post
                    $possibleFriend = $possibleFriends->pop();
                    $heart->user_id = $possibleFriend->id;
                });
                $post->hearts()->saveMany($hearts);
            });
    }

    protected function getRandomElement($array){
        $randomIndex = array_rand($array);
        return $array[$randomIndex];
    }

    protected function getPossibleFriends($post){
        $possibleFriends = new Collection;
        $user = User::findOrFail($post->user_id);
        $possibleFriends = $possibleFriends->push($user); //INCLUIDO CASO DE VISIBILIDAD PRIVADA UNICO USUARIO

        // friend visibility
        if($post->visibility == Post::JUST_FOR_FRIENDS){
            $friends= $user->friends()->where('status', User::FRIENDSHIP_ACCEPTED)->get(); //se debe usar get aqui para convertirlo en coleccion y poder usar random
            if($friends->isNotEmpty()){
                $friends->each(function ($friend) use ($possibleFriends){
                    $possibleFriends->push($friend);
                });
            }
        }

        // friends of friend visibility
        if($post->visibility == Post::FRIENDS_OF_FRIENDS){

            $friends= $user->friends()->where('status', User::FRIENDSHIP_ACCEPTED)->get(); //se debe usar get aqui para convertirlo en coleccion y poder usar random
            if($friends->isNotEmpty()){
                $friends->each(function ($friend) use ($possibleFriends){
                    $possibleFriends->push($friend);
                });
                $randomFriend = $friends->random();
                $friendsOfRandomFriend = $randomFriend->friends()->where('status', User::FRIENDSHIP_ACCEPTED)->get();
                if($friendsOfRandomFriend->isNotEmpty()){
                    $friendsOfRandomFriend->each(function ($friend) use ($possibleFriends){
                        $possibleFriends->push($friend);
                    });
                }
            }
        }

        // public visibility
        if($post->visibility == Post::FOR_EVERYONE){
            $possibleFriends = User::all();
        }

        return $possibleFriends;
    }
}
