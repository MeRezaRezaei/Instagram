<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            // keys
            $table->id()->comment('primary key of the posts table');
            $table->unsignedBigInteger('user_id')->comment('Foreign key to the table users from posts table');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('description',60)->comment('post description will appear here')->nullable();
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
        Schema::dropIfExists('posts');
    }
}
