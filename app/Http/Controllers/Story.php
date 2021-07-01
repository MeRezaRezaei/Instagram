<?php

namespace App\Http\Controllers;

use App\Actions\Response;
use App\Models\User;
use Illuminate\Http\Request;

class Story extends Controller
{
    use Response;

    public function get_story_feed(){
        $user = User::find($this->LoggedInUserId);
        $following_users = $user->following;


        $feed_stories = [];
        foreach ($following_users as $following_user){

            $stories = $following_user->story;
            foreach ($stories as $story)
                $feed_stories[] = [
                    'id'=>$story->id,
                    'path'=>$story->path,
                    'owner_id'=>$following_user->id,
                ]
                ;

        }


        return $this->Make_Response(
            'user story feed',
            200,
            $feed_stories);

    }

    public function new_story(Request $request){
        $story_pic_path = null;
        $message = null;


        if ($request->hasFile('story_pic')){

            if($request->file('story_pic')->isValid()){

                $story_pic_extension = $request->file('story_pic')
                    ->extension();
                if (
                    $story_pic_extension == 'png'
                    || $story_pic_extension == 'jpg'
                    || $story_pic_extension == 'jpeg'
                )
                    $story_pic_path = $request->file('story_pic')
                        ->store('images')
                    ;

            }
            else
                $message = 'story_pic file was not upload successfully'
                ;

        }
        else
            $message = 'story_pic file does not exist'
            ;


        if ($message)
            return $this->Make_Error($message,400)
                ;


        $story = new \App\Models\Story();
        $story->path = $story_pic_path;
        $story->user_id = $this->LoggedInUserId;
        $story->save();


        return $this->Make_Response(
            'story stored successfully',
            201
        );

    }
}
