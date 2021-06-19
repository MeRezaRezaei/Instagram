<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('Foreign key to the table users from Comments table');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('post_id')->comment('Foreign key to the table posts from comments table');
            $table->foreign('user_id')->references('id')->on('posts');
            $table->unsignedBigInteger('replay_to_id')->comment('Foreign key to the table users from posts table')->nullable();
            $table->foreign('replay_to_id')->references('id')->on('comments');
            $table->text('comment')->comment('comment string for this post');
            $table->softDeletes();
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
        Schema::dropIfExists('comments');
    }
}
