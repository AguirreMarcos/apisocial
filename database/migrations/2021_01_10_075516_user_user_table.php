<?php

use App\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_user', function (Blueprint $table) {

            $table->bigInteger('self_id')->unsigned()->index();
            $table->bigInteger('friend_id')->unsigned()->index();
            $table->string('status')->default(User::FRIENDSHIP_PENDING);
            $table->string('is_sender')->default(User::FRIENDSHIP_RECEIVER);
            $table->timestamps();

            $table->foreign('self_id')->references('id')->on('users');
            $table->foreign('friend_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_user');
    }
}
