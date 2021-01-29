<?php

namespace App\Traits;

use App\Post;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpKernel\Exception\HttpException;


trait UserFunctions{

    protected function isHeartedByUser(User $user, Model $instance){
        $hearts = $instance->hearts;
        foreach ($hearts as $heart) { // importante usar foreach y no each()
            if($heart->user_id == $user->id){
                return true;
            }
        }
        return false;
    }

    protected function canReach(User $user, Model $instance){

        $canHeart = false;

        $instanceOwner = User::findOrFail($instance->user_id);

        switch ($instance->visibility) {
            case Post::JUST_FOR_ME:
                $user->id == $instance->user_id ? $canHeart = true : $canHeart = false;
                break;
            case Post::JUST_FOR_FRIENDS:
                $this->isFriendOf($user, $instanceOwner) || $user->id == $instanceOwner->id ? $canHeart = true : $canHeart = false;
                break;
            case Post::FRIENDS_OF_FRIENDS:
                $friends = $instanceOwner->friends()->get();
                foreach ($friends as $friend) {
                    if($this->isFriendOf($user, $friend) || $user->id == $friend->id){
                        $canHeart = true;
                        break; // we break at the first that retrieves true
                    }
                }
                break;
            case Post::FOR_EVERYONE:
                $canHeart = true;
                break;
        }

        return $canHeart;
    }

    protected function isFriendOf(User $user, User $possibleFriend){

        /* $friendsWithoutStatus = $possibleFriend->friends;
        $friends = new Collection;
        foreach ($friendsWithoutStatus as $friend) {
            if($friend->pivot->status == User::FRIENDSHIP_ACCEPTED){
                $friends->push($friend);
            }
        }
        if($friends->isNotEmpty()){
            foreach ($friends as $friend) {
                if($friend->id == $user->id){
                    return true;
                }
            }
        }
        return false; */
        $friends = $user->friends()->where('status', User::FRIENDSHIP_ACCEPTED)->get();
        if($friends->isNotEmpty()){
            foreach ($friends as $friend) {
                if($friend->id == $possibleFriend->id){
                    return true;
                }
            }
        }
        return false;

    }

    protected function isSender(User $user, User $possibleFriend){

        /* $friendsWithoutSender = $user->friends; //guardo la coleccion de amigos
        $friends = new Collection; // creo una nueva coleccion
        foreach ($friendsWithoutSender as $friend) { //para cada amigo de la coleccion
            if($friend->pivot->is_sender == User::FRIENDSHIP_SENDER){ // en caso de que este amigo sea receptor de invitacion
                $friends->push($friend); //agrego amigo como posible receptor
            }
        }
        if($friends->isNotEmpty()){ // si la lista de dichos amigos no esta vacía la recorro
            foreach ($friends as $friend) {
                if($friend->id == $possibleFriend->id){ // si el id que estoy recorriendo coincide con el de mi posible amigo devuelvo true
                    return true;
                }
            }
        }
        return false; */

        $friendsWhereIAmSender = $user->friends()->where('is_sender', User::FRIENDSHIP_SENDER)->get();
        if($friendsWhereIAmSender->isNotEmpty()){ // si la lista de dichos amigos no esta vacía la recorro
            foreach ($friendsWhereIAmSender as $friend) {
                if($friend->id == $possibleFriend->id){ // si el id que estoy recorriendo coincide con el de mi posible amigo devuelvo true
                    return true;
                }
            }
        }
        return false;
    }

    protected function invitationAlreadyExists(User $user, User $possibleFriend){

        /* $friendsWithoutStatus = $possibleFriend->friends;
        $friends = new Collection;
        foreach ($friendsWithoutStatus as $friend) {
            if($friend->pivot->status == User::FRIENDSHIP_PENDING){
                $friends->push($friend);
            }
        }
        if($friends->isNotEmpty()){
            foreach ($friends as $friend) {
                if($friend->id == $user->id){
                    return true;
                }
            }
        }
        return false; */
        $friendsPending = $user->friends()->where('status', User::FRIENDSHIP_PENDING)->get();
        if($friendsPending->isNotEmpty()){
            foreach ($friendsPending as $friend) {
                if($friend->id == $possibleFriend->id){
                    return true;
                }
            }
        }
        return false;

    }

    protected function verifyUser(User $user, Model $instance){
        if($user->id != $instance->user_id){
            throw new HttpException(422, 'User must be the creator of the content');
        }
    }

    protected function sendInvitation(User $user, User $friend)
    {
        $user->friends()->attach($friend->id, ['status' => User::FRIENDSHIP_PENDING, 'is_sender' => User::FRIENDSHIP_SENDER]);
        $friend->friends()->attach($user->id, ['status' => User::FRIENDSHIP_PENDING, 'is_sender' => User::FRIENDSHIP_RECEIVER]);
    }
    protected function removeFriendRelation(User $user, User $friend)
    {
        $user->friends()->detach($friend->id);
        $friend->friends()->detach($user->id);
    }

    protected function acceptInvitation(User $user, User $friend){
        $user->friends()->updateExistingPivot($friend->id, ['status' => User::FRIENDSHIP_ACCEPTED]);
        $friend->friends()->updateExistingPivot($user->id, ['status' => User::FRIENDSHIP_ACCEPTED]);
    }

}
