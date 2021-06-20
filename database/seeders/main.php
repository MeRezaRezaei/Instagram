<?php

namespace Database\Seeders;

use App\Models\Comments;
use App\Models\Follows;
use App\Models\Likes;
use App\Models\Messages;
use App\Models\Pictures;
use App\Models\Posts;
use App\Models\Story;
use App\Models\User;
use Illuminate\Database\Seeder;

class main extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $User1 = User::create([
            'name' => 'user1',
            'email' => '',
            'password' =>'adfa'
        ]);
        $User2 = User::create([
            'name' => 'user2',
            'email' => 'merezarezasdasaei@gmail.comweqwe',
            'password' =>'adfa'
        ]);
        $User3 = User::create([
            'name' => 'user3',
            'email' => 'merezarezsdasdaei@gmail.comgdfgdfg',
            'password' =>'adfa'
        ]);
        $User4 = User::create([
            'name' => 'user4',
            'email' => 'merezarasdaezaei@gmail.comasdfasdf',
            'password' =>'adfa'
        ]);


        $Post1 = Posts::create([
            'description' => 'this is seeder description 1',
            'user_id' => $User1->id
        ]);
        $Post2 = Posts::create([
            'description' => 'this is seeder description 2',
            'user_id' => $User2->id
        ]);
        $Post3 = Posts::create([
            'description' => 'this is seeder description 3',
            'user_id' => $User2->id
        ]);
        $Post4 = Posts::create([
            'description' => 'this is seeder description 4',
            'user_id' => $User3->id
        ]);


        Follows::create([
            'following_id'=> $User1->id,
            'follower_id'=> $User2->id
        ]);
        Follows::create([
            'following_id'=> $User1->id,
            'follower_id'=> $User3->id
        ]);
        Follows::create([
            'following_id'=> $User2->id,
            'follower_id'=> $User4->id
        ]);

        Follows::create([
            'following_id'=> $User4->id,
            'follower_id'=> $User1->id
        ]);


        Messages::create([
            'from_id'=>$User1->id,
            'to_id'=>$User2->id,
            'text'=>'seeder message 1',
            'is_read'=>false
        ]);
        Messages::create([
            'from_id'=>$User1->id,
            'to_id'=>$User3->id,
            'text'=>'seeder message 2',
            'is_read'=>false
        ]);
        Messages::create([
            'from_id'=>$User1->id,
            'to_id'=>$User4->id,
            'text'=>'seeder message 3',
            'is_read'=>false
        ]);
        Messages::create([
            'from_id'=>$User2->id,
            'to_id'=>$User4->id,
            'text'=>'seeder message 4',
            'is_read'=>false
        ]);


        Story::create([
            'user_id'=>$User1->id,
            'path'=>'some path 1'
        ]);
        Story::create([
            'user_id'=>$User1->id,
            'path'=>'some path 2'
        ]);
        Story::create([
            'user_id'=>$User3->id,
            'path'=>'some path 3'
        ]);

        Pictures::create([
            'post_id'=>$Post1->id,
            'path'=> 'post picture path 1'
        ]);
        Pictures::create([
            'post_id'=>$Post2->id,
            'path'=> 'post picture path 2'
        ]);
        Pictures::create([
            'post_id'=>$Post1->id,
            'path'=> 'post picture path 3'
        ]);

        Likes::create([
            'user_id'=> $User2->id,
            'post_id'=>$Post4->id
        ]);
        Likes::create([
            'user_id'=> $User3->id,
            'post_id'=>$Post2->id
        ]);
        Likes::create([
            'user_id'=> $User1->id,
            'post_id'=>$Post2->id
        ]);

        $comment1 = Comments::create([
            'user_id'=>$User1->id,
            'post_id'=>$Post1->id,
            'replay_to_id' => null,
            'comment'=>'first comment 1'
        ]);
        $comment2 = Comments::create([
            'user_id'=>$User2->id,
            'post_id'=>$Post1->id,
            'replay_to_id' =>  $comment1->id,
            'comment'=>'first comment 2'
        ]);
        Comments::create([
            'user_id'=>$User3->id,
            'post_id'=>$Post3->id,
            'replay_to_id' => null,
            'comment'=>'first comment 3'
        ]);
        Comments::create([
            'user_id'=>$User1->id,
            'post_id'=>$Post2->id,
            'replay_to_id' =>$comment2->id,
            'comment'=>'first comment 4'
        ]);




    }


}
