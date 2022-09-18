<?php

namespace App\Http\Controllers;

use App\Models\Comments;
use App\Models\Posts;

trait Fetch
{
    protected function fetch_Post($post_id){
        $post = Posts::find($post_id);
        $post_owner = $post->user;
        $post_pictures = $post->pics;


        $pictures = [];
        foreach ($post_pictures as $post_picture)
            $pictures[] = $post_picture->path
            ;


        $post_likes_count = $post->likes()->count();


        $post_comments = $post->comments()
            ->whereNull('replay_to_id')
            ->get();


        $comments = [];
        foreach ($post_comments as $post_comment)
            $comments[] =[
                'id'=>$post_comment->id,
                'user_id'=>$post_comment->user_id,
                'comment'=>$post_comment->comment,
                'child_replay'=>$this->fetch_comment($post_comment->id)
            ]
            ;

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

    protected function fetch_comment($comment_id){
        $comment = Comments::find($comment_id);
        if ($comment_id === null)
            return null
                ;


        $comment_child_count = $comment->replay_child()->count();
        if ($comment_child_count === 0)
            return null
                ;


        $children = $comment->replay_child()
            ->where('post_id',$comment->post_id)
            ->get();


        $comment_children = [];
        foreach ($children as $child){
            $comment_children[] = [
                'id'=>$child->id,
                'user_id'=>$child->user_id,
                'post_id'=>$child->post_id,
                'comment'=>$child->comment,
                'child_replay'=>$this->fetch_comment($child->id)
            ];
        }


        return $comment_children;
    }
}