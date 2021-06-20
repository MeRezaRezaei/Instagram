<?php

use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('registration',
    [\App\Http\Controllers\API::class,'registration']
);
Route::get('login',
    [\App\Http\Controllers\API::class,'login']
);
Route::get('new_post',
    [\App\Http\Controllers\API::class,'new_post']
);
Route::get('new_story',
    [\App\Http\Controllers\API::class,'new_story']
);
Route::get('send_dm',
    [\App\Http\Controllers\API::class,'send_dm']
);
Route::get('get_profile',
    [\App\Http\Controllers\API::class,'get_profile']
);
Route::get('set_profile',
    [\App\Http\Controllers\API::class,'set_profile']
);
Route::get('send_comment',
    [\App\Http\Controllers\API::class,'send_comment']
);
Route::get('increase_like',
    [\App\Http\Controllers\API::class,'increase_like']
);
Route::get('get_post',
    [\App\Http\Controllers\API::class,'get_post']
);
Route::get('get_post_feed',
    [\App\Http\Controllers\API::class,'get_post_feed']
);
Route::get('get_profile_posts',
    [\App\Http\Controllers\API::class,'get_profile_posts']
);
Route::get('get_dialog',
    [\App\Http\Controllers\API::class,'get_dialog']
);
Route::get('get_story_feed',
    [\App\Http\Controllers\API::class,'get_story_feed']
);
Route::get('delete_post',
    [\App\Http\Controllers\API::class,'delete_post']
);
Route::get('follow',
    [\App\Http\Controllers\API::class,'follow']
);
Route::get('unfollow',
    [\App\Http\Controllers\API::class,'unfollow']
);
Route::get('delete_comment',
    [\App\Http\Controllers\API::class,'delete_comment']
);
Route::get('get_post_comments',
    [\App\Http\Controllers\API::class,'get_post_comments']
);
Route::get('get_following_lists',
    [\App\Http\Controllers\API::class,'get_following_lists']
);
Route::get('get_follower_lists',
    [\App\Http\Controllers\API::class,'get_follower_lists']
);




