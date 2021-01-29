<?php

namespace App\Http\Controllers\User;

use App\User;
use Illuminate\Http\Request;
use App\Traits\UserFunctions;
use App\Http\Controllers\ApiController;

class UserFriendController extends ApiController
{
    use UserFunctions;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(User $user)
    {
        $friends = $user->friends()->where('status', User::FRIENDSHIP_ACCEPTED)->get();
        return $this->showAll($friends);
    }

    public function invite(User $user, User $friend){

        if($this->isFriendOf($user, $friend)){
            return $this->errorResponse('You are already friend of this user', 422);
        }

        if($this->invitationAlreadyExists($user, $friend)){
            return $this->errorResponse('There is already an invitation pending between these users', 422);
        }

        if($user->id == $friend->id){
            return $this->errorResponse('User can not send self invitation', 403);
        }

        $this->sendInvitation($user, $friend);

        return $this->showOne($user->friends()->where('friend_id', $friend->id)->first());
    }

    public function accept(User $user, User $friend){

        if($this->isFriendOf($user, $friend)){
            return $this->errorResponse('You are already friend of this user', 422);
        }

        if(!$this->invitationAlreadyExists($user, $friend)){
            return $this->errorResponse('There is no invitation to accept', 422);
        }

        if($this->isSender($user, $friend)){
            return $this->errorResponse('This user is the invitation sender and can not accept his own invitation', 403);
        }

        $this->acceptInvitation($user, $friend);

        return $this->showMessage('Friendship invitation accepted');
    }

    public function decline(User $user, User $friend){

        if($this->isFriendOf($user, $friend)){
            return $this->errorResponse('You are already friend of this user', 422);
        }

        if(!$this->invitationAlreadyExists($user, $friend)){
            return $this->errorResponse('There is no invitation to decline', 422);
        }

        if($this->isSender($user, $friend)){
            return $this->errorResponse('This user is the invitation sender and can not decline his own invitation', 403);
        }

        $this->removeFriendRelation($user, $friend);

        return $this->showMessage('Friendship invitation declined');
    }

    public function cancel(User $user, User $friend){

        if($this->isFriendOf($user, $friend)){
            return $this->errorResponse('You are already friend of this user', 422);
        }

        if(!$this->invitationAlreadyExists($user, $friend)){
            return $this->errorResponse('There is no invitation to cancel', 422);
        }

        if(!$this->isSender($user, $friend)){
            return $this->errorResponse('This user is the invitation receiver and can not cancel this invitation, use decline instead', 403);
        }

        $this->removeFriendRelation($user, $friend);

        return $this->showMessage('Friendship invitation canceled');
    }

    public function delete(User $user, User $friend){

        if(!$this->isFriendOf($user, $friend)){
            return $this->errorResponse('You are not friend of this user', 403);
        }

        $this->removeFriendRelation($user, $friend);

        return $this->showMessage('Friendship deleted');
    }

    public function getFriendsWhereSender(User $user){
        $friendsWhereSender =  $user->friends()->where('is_sender', User::FRIENDSHIP_SENDER)->get();
        return $this->showAll($friendsWhereSender);
    }

    public function getFriendsWhereReceiver(User $user){
        $friendsWhereReceiver = $user->friends()->where('is_sender', User::FRIENDSHIP_RECEIVER)->get();
        return $this->showAll($friendsWhereReceiver);
    }

}
