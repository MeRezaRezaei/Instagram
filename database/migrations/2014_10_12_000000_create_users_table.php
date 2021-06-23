<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            //$table->string('username',200)->nullable()->comment('username of the registering user');
            $table->string('profile_pic_path',255)->nullable()->comment('path to the user profile picture');
            $table->string('last_name',255)->nullable()->comment('last name of the registering user');
            $table->string('bio',255)->nullable()->comment('biography of the registering user');
            $table->string('name',255);
            $table->string('email',200)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
