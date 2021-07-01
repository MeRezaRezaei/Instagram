<?php

namespace App\Http\Controllers;

use App\Models\Posts;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Actions\Response;

class Post extends Controller
{

    use Response;
    // todo decide whether validate photo is in the same level of abstraction as create new post
    protected function ValidatePicture(Request $request){
        $AllFiles = $request->allFiles();
        foreach ($AllFiles as $Key => $file){
            $message = false;
            $extension = $file->extension();
            if (!$file->isValid()){
                $message = 'file '.$Key.'did not upload correctly';
            }
            if (
            !(
                $extension == 'png'
                || $extension == 'jpg'
                || $extension == 'jpge'
            )
            ){
                $message = 'file '.$Key.' extension '.$extension.' is not valid';
            }
            if ($message !== false){
                return $message;
            }
        }
    }
    public function create_new_post(Request $request){
        $validator = Validator::make($request->all(),[
            'description'=>'required|string|max:100',
        ],[
            'description.required'=>'description is required',
            'description.string' => 'description must be string',
            'description.max' => 'max allowed description is 100 characters',
        ]);

        if ($validator->fails())
            return $this->Make_Error($validator->errors(),400);

        $files_count = count($request->allFiles());
        if ($files_count == 0){
            return $this->Make_Error(
                'posts must have at least one picture',
                400
            );
        }
        elseif ($files_count > 10){
            return $this->Make_Error(
                'posts can not have more than ten pictures',
                400
            );
        }


        if (($message = $this->ValidatePicture($request))!= false)
            return $this->Make_Error(['invalid file' => $message], 400);

        $PicturesPath = [];
        foreach ($request->allFiles() as $file){
            $path = $file->store('images');
            $PicturesPath[] = ['path'=>$path];
        }
        $post = new Posts;
        $post->description = $request->description;
        $post->user_id = $this->LoggedInUserId;
        $post->save();
        $post->pics()->createmany($PicturesPath);


        return $this->Make_Response('post created successfully',201);
    }


    public function get_post(Request $request){
        $Validate = Validator::make($request->all(),[
            'post_id'=>'required|integer|exists:App\Models\Posts,id'
        ]);

        if ($Validate->fails())
        return $this->Make_Error($Validate->errors(),400)
        ;

        $post = Posts::where('id',$request->post_id)->first();

        if ($post === null)
        return $this->Make_Error('this post does not exist or soft deleted',400)
        ;

        $post_full = $this->fetch_Post($request->post_id);
        return $this->Make_Response('full post information',200,$post_full);

    }

    public function get_post_feed(){
        $user = User::find($this->LoggedInUserId);
        $following_users = $user->following;
        $feed_posts = [];
        foreach ($following_users as $following_user){
            $posts = $following_user->posts;
            foreach($posts as $post){
                $feed_posts[] = $this->fetch_Post($post->id);
            }
        }
        return $this->Make_Response('post feed',200,$feed_posts);
    }

    public function get_profile_posts(){
        $user = User::find($this->LoggedInUserId);
        $user_posts = $user->posts;
        $self_posts = [];
        foreach ($user_posts as $post){
            $self_posts[] = $this->fetch_Post($post->id);
        }
        return $this->Make_Response('all of your profile posts',200,$self_posts);
    }



    protected function fetch_Post($post_id){
        $post = Posts::find($post_id);
        $post_owner = $post->user;
        $post_pictures = $post->pics;
        $pictures = [];
        foreach ($post_pictures as $post_picture){
            $pictures[] = $post_picture->path;
        }
        $post_likes_count = $post->likes()->count();
        $post_comments = $post->comments()->whereNull('replay_to_id')->get();
        //dd($post_comments->count());
        $comments = [];
        foreach ($post_comments as $post_comment){
            $comments[] =[
                'id'=>$post_comment->id,
                'user_id'=>$post_comment->user_id,
                'comment'=>$post_comment->comment,
                'child_replay'=>$this->fetch_comment($post_comment->id)
            ];
        }
        return [
            'id'=>$post->id,
            'description'=>$post->description,
            'owner'=>[
                'id'=>$post_owner->id,
                'name'=>$post_owner->name,
                'email'=>$post_owner->email,
                'last_name'=>$post_owner->last_name,
                'profile_pic_path'=>$post_owner->profile_pic_path
            ],
            'pictures'=>$pictures,
            'likes'=>$post_likes_count,
            'comments'=>$comments
        ];
    }
}
