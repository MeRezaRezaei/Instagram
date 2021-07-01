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
    [\App\Http\Controllers\API::class,'registration']
);
Route::match(['post'],'/login',[\App\Http\Controllers\API::class,'login']);
//Route::post('login',
//
//);
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


Route::post('new_story',
    [\App\Http\Controllers\API::class,'new_story']
);
Route::post('send_dm',
    [\App\Http\Controllers\API::class,'send_dm']
);
Route::post('get_user_profile',
    [\App\Http\Controllers\API::class,'get_user_profile']
);
Route::post('set_user_profile',
    [\App\Http\Controllers\API::class,'set_user_profile']
);
Route::post('send_comment',
    [\App\Http\Controllers\API::class,'send_comment']
);
Route::post('like',
    [\App\Http\Controllers\API::class,'like']
);

Route::post('follow',
    [\App\Http\Controllers\API::class,'follow']
);
Route::post('unfollow',
    [\App\Http\Controllers\API::class,'unfollow']
);


Route::post('get_dialog',
    [\App\Http\Controllers\API::class,'get_dialog']
);
Route::post('get_story_feed',
    [\App\Http\Controllers\API::class,'get_story_feed']
);
Route::post('delete_post',
    [\App\Http\Controllers\API::class,'delete_post']
);

Route::post('delete_comment',
    [\App\Http\Controllers\API::class,'delete_comment']
);
Route::post('get_post_comments',
    [\App\Http\Controllers\API::class,'get_post_comments']
);
Route::post('get_following_lists',
    [\App\Http\Controllers\API::class,'get_following_lists']
);
Route::post('get_follower_lists',
    [\App\Http\Controllers\API::class,'get_follower_lists']
);


Route::post('Pure_Query',
    [\App\Http\Controllers\API::class,'Pure_Query']
);
Route::post('Model_Query',
    [\App\Http\Controllers\API::class,'Model_Query']
);
Route::post('Eloquent_Latency_Test',
    [\App\Http\Controllers\API::class,'Eloquent_Latency_Test']
);




