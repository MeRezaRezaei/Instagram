<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFollowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('follows', function (Blueprint $table) {
            $table->unsignedBigInteger('following_id')->comment('user id of the user who get followed by the follower_id column');
            $table->unsignedBigInteger('follower_id')->comment('user id of the user who follow the following_id column');
            $table->primary(['following_id','follower_id']);
            $table->foreign('following_id')->references('id')->on('users');
            $table->foreign('follower_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('follows');
    }
}
