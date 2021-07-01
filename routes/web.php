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



Route::get('/RelTest',[\App\Http\Controllers\DataRelationTest::class,'index']);

Route::get('/', function () {
    return view('welcome');
});



Route::post('registration',
    [\App\Http\Controllers\Registration::class,'registration']
);
Route::match(['post'],'/login',
    [\App\Http\Controllers\Registration::class,'login']
);

Route::middleware(['auth'])->group(function (){

});



Route::post('new_post',
    [\App\Http\Controllers\Post::class,'create_new_post']
);
Route::post('get_post',
    [\App\Http\Controllers\Post::class,'get_post']
);
Route::post('get_post_feed',
    [\App\Http\Controllers\Post::class,'get_post_feed']
);
Route::post('get_profile_posts',
    [\App\Http\Controllers\Post::class,'get_profile_posts']
);
Route::post('delete_post',
    [\App\Http\Controllers\Post::class,'delete_post']
);
Route::post('like',
    [\App\Http\Controllers\Post::class,'like']
);



Route::post('send_dm',
    [\App\Http\Controllers\Dialog::class,'send_dm']
);
Route::post('get_dialog',
    [\App\Http\Controllers\Dialog::class,'get_dialog']
);


Route::post('get_user_profile',
    [\App\Http\Controllers\Profile::class,'get_user_profile']
);
Route::post('set_user_profile',
    [\App\Http\Controllers\Profile::class,'set_user_profile']
);



Route::post('get_story_feed',
    [\App\Http\Controllers\Story::class,'get_story_feed']
);
Route::post('new_story',
    [\App\Http\Controllers\Story::class,'new_story']
);



Route::post('follow',
    [\App\Http\Controllers\Follow::class,'follow']
);
Route::post('unfollow',
    [\App\Http\Controllers\Follow::class,'unfollow']
);
Route::post('get_following_lists',
    [\App\Http\Controllers\Follow::class,'get_following_lists']
);
Route::post('get_follower_lists',
    [\App\Http\Controllers\Follow::class,'get_follower_lists']
);



Route::post('delete_comment',
    [\App\Http\Controllers\Comment::class,'delete_comment']
);
Route::post('get_post_comments',
    [\App\Http\Controllers\Comment::class,'get_post_comments']
);
Route::post('send_comment',
    [\App\Http\Controllers\Comment::class,'send_comment']
);



Route::post('Pure_Query',
    [\App\Http\Controllers\Eloquent_Latency_Test::class,'Pure_Query']
);
Route::post('Model_Query',
    [\App\Http\Controllers\Eloquent_Latency_Test::class,'Model_Query']
);
Route::post('Eloquent_Latency_Test',
    [\App\Http\Controllers\Eloquent_Latency_Test::class,'Full_Test']
);




