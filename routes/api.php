<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/* Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
}); */

/**
 * Users
 */
Route::resource('users', 'User\UserController')->except('create', 'edit');
Route::resource('users.posts', 'User\UserPostController')->except('create', 'show', 'edit');
Route::resource('users.comments', 'User\UserCommentController')->only('index');
Route::resource('users.hearts', 'User\UserHeartController')->only('index');
Route::resource('users.posts.comments', 'User\UserPostCommentController')->except('create', 'show', 'edit');
Route::resource('users.posts.hearts', 'User\UserPostHeartController')->only('store');
Route::delete('users/{user}/posts/{post}/hearts/{heart?}', 'User\UserPostHeartController@destroy')->name('users.posts.hearts.destroy');
Route::resource('users.posts.comments.hearts', 'User\UserPostCommentHeartController')->only('store');
Route::delete('users/{user}/posts/{post}/comments/{comment}/hearts/{heart?}', 'User\UserPostCommentHeartController@destroy')->name('users.posts.comments.hearts.destroy');

/**
 * Users mailing
 */
Route::get('users/verify/{token}', 'User\UserController@verify')->name('users.verify');
Route::get('users/{user}/resend', 'User\UserController@resend')->name('users.resend');

/**
 * friendship routes
 */
Route::resource('users.friends', 'User\UserFriendController')->only('index');
Route::get('users/{user}/sender', 'User\UserFriendController@getFriendsWhereSender')->name('users.friends.sender');
Route::get('users/{user}/receiver', 'User\UserFriendController@getFriendsWhereReceiver')->name('users.friends.receiver');
Route::post('users/{user}/friends/{friend}/invite', 'User\UserFriendController@invite')->name('users.friends.invite');
Route::post('users/{user}/friends/{friend}/accept', 'User\UserFriendController@accept')->name('users.friends.accept');
Route::post('users/{user}/friends/{friend}/decline', 'User\UserFriendController@decline')->name('users.friends.decline');
Route::post('users/{user}/friends/{friend}/cancel', 'User\UserFriendController@cancel')->name('users.friends.cancel');
Route::post('users/{user}/friends/{friend}/delete', 'User\UserFriendController@delete')->name('users.friends.delete');

/**
 * Posts
 */
Route::resource('posts', 'Post\PostController')->only('index', 'show', 'destroy');
Route::resource('posts.hearts', 'Post\PostHeartController')->only('index');
Route::resource('posts.comments', 'Post\PostCommentController')->only('index');

/**
 * Comments
 */
Route::resource('comments', 'Comment\CommentController')->only('index', 'show', 'destroy');
Route::resource('comments.hearts', 'Comment\CommentHeartController')->only('index');

/**
 * Hearts
 */
Route::resource('hearts', 'Heart\HeartController')->only('index', 'show', 'destroy');
Route::get('hearts/{heart}/content', 'Heart\HeartController@getContentHearted')->name('hearts.content');

/**
 * Oauth
 */
Route::post('oauth/token', '\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken');
