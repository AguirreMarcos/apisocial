<?php

namespace App\Http\Controllers\User;

use App\User;
use App\Mail\UserCreated;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;
use App\Transformers\UserTransformer;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Storage;

class UserController extends ApiController
{

    public function __construct(){
        /* parent::__construct(); */
        //$this->middleware('client.credentials')->only('store', 'resend');
        $this->middleware('auth:api')->except('store', 'resend', 'verify');
        $this->middleware('transform.input:' . UserTransformer::class)->only('store', 'update');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return $this->showAll($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'nickname' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            /* 'email' => ['required', 'email', Rule::unique('users')->whereNull('deleted_at')], */
            'profile_img_path' => 'file|mimes:jpeg,jpg,png',
            'password' => 'required|min:6|confirmed',
        ];

        $this->validate($request, $rules);

        $fieldsOfUser = $request->all();
        $fieldsOfUser['password'] = bcrypt($request->password);
        $fieldsOfUser['verified'] = User::USER_NOT_VERIFIED;
        $fieldsOfUser['verification_token'] = User::generateVerificationToken();
        $fieldsOfUser['admin'] = User::REGULAR_USER;
        if($request->has('profile_img_path')){
            $fieldsOfUser['profile_img_path'] = $request->profile_img_path->store('', 'profile_img_driver');
        }


        $user = User::create($fieldsOfUser);

        return $this->showOne($user, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return $this->showOne($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {

        $rules = [
            'email' => 'email|unique:users,email,' . $user->id, // de esta forma se exceptua el mail actual del usuario de la validacion unique.
            'password' => 'min:6|confirmed',
            'admin' => 'in:' . User::ADMIN_USER . ',' . User::REGULAR_USER,
        ];

        $this->validate($request, $rules);

        if($request->has('name')){
            $user->name = $request->name;
        }
        if($request->has('email') && $user->email != $request->email){
            $user->verified = User::USER_NOT_VERIFIED;
            $user->verification_token = User::generateVerificationToken();
            $user->email = $request->email;
        }
        if($request->has('password')){
            $user->password = bcrypt($request->password);
        }
        if($request->has('admin')){
            //esta accion solo la podrá realizar otro admin
            if(!$user->isVerified()){

                return $this->errorResponse('Only verified users can change his own admin status', 409);
            }
            $user->admin = $request->admin;
        }

        if($request->has('profile_img_path')){
            Storage::disk('profile_img_driver')->delete($user->profile_img_path);
            $user->profile_img_path = $request->profile_img_path->store('', 'profile_img_driver');
        }

        if(!$user->isDirty()){

            return $this->errorResponse('At least one parameter must be changed to update user info.', 422);
        }

        $user->save();

        return $this->showOne($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        Storage::disk('profile_img_driver')->delete($user->profile_img_path);
        $user->delete();
        return $this->showOne($user);
    }

    public function verify($token){

        $user = User::where('verification_token', $token)->first();
        if(is_null($user)){
            return $this->errorResponse('Verification token invalid', 422);
        }
        $user->verified = User::USER_VERIFIED;
        $user->verification_token = null;

        $user->save();

        return $this->showMessage('The account has been verified');
    }

    public function resend(User $user){
        if($user->isVerified()){
            return $this->errorResponse('This user has already been verified', 409);
        }
        retry(5, function() use($user){
            Mail::to($user->email)->send(new UserCreated($user));
        }, 100);

        return $this->showMessage('The verification email has been sent again');
    }
}
