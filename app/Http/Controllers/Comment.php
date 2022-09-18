<?php

namespace App\Http\Controllers;

use App\Actions\Response;
use App\Models\Comments;
use App\Models\Posts;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Comment extends Controller
{
    use Response,Fetch;

    public function get_post_comments(Request $request){
        $Validate  = Validator::make($request->all(),[
            'post_id'=>'required|integer'
        ]);
        if ($Validate->fails())
            return $this->Make_Error($Validate->errors(),400)
                ;


        $post = Posts::find(1);

        if ($post === null)
            return $this->Make_Error(
                'this post does not exist or deleted',
                400)
                ;


        $post_comments = $post->comments()
            ->where('post_id',$post->id)
            ->whereNull('replay_to_id')
            ->get();

        $comments = [];
        foreach ($post_comments as $post_comment)
            $comments[] =[
                'id'=>$post_comment->id,
                'user_id'=>$post_comment->user_id,
                'post_id'=>$post_comment->post_id,
                'comment'=>$post_comment->comment,
                'child_replay'=>$this->fetch_comment($post_comment->id)
            ]
            ;


        return $this->Make_Response(
            'all comments for this post',
            200,
            $comments);

    }

    public function delete_comment(Request $request){
        $Validate = Validator::make($request->all(),[
            'comment_id'=>'required|integer'
        ]);
        if ($Validate->fails())
            return $this->Make_Error($Validate->errors(),400)
                ;



        $user = User::find($this->LoggedInUserId);

        $trashed_comment = Comments::where('id',$request->comment_id)
            ->withTrashed()
            ->first();


        $message = '';
        if ($trashed_comment === null){
            $message = 'this comment does not exist or deleted before';
            goto Permanently_Deleted;
        }
        else{

            if ($trashed_comment->user_id != $user->id )
                return $this->Make_Error('this comment does not belongs to you and you dont have the right to delete it.',
                    400)
                    ;


            if ($trashed_comment->deleted_at === null){
                $trashed_comment->delete();
                $message = 'comment deleted successfully';
            }
            else
                $message = 'this comment have been soft deleted before'
                ;

        }


        Permanently_Deleted:
        return $this->Make_Response($message,200);

    }

    public function send_comment(Request $request){
        $Validate = Validator::make($request->all(),[
            'post_id'=>'required|integer|exists:App\Models\Posts,id',
            'comment'=>'required|string|max:255',
            'replay_to'=>'integer|exists:App\Models\Comments,id'
        ]);
        if ($Validate->fails())
            return $this->Make_Error($Validate->errors(),400)
                ;


        $post = Posts::where('id',$request->post_id)->first();
        if ($post === null)
            return $this->Make_Error(
                'this post does not exist or soft deleted',400)
                ;


        $replay_to = $request->replay_to;
        $comment = Comments::where('id',$replay_to)->first();

        if ($comment === null)
            return $this->Make_Error(
                'this comment does not exist or soft deleted',400)
                ;


        if($post->id !== $comment->post_id)
            return $this->Make_Error(
                'the comment you want to replay on does not belong to selected post',400)
                ;


        $comment = $post->comments()->create([
            'replay_to_id'=>$request->replay_to,
            'user_id'=>$this->LoggedInUserId,
            'comment'=>$request->comment
        ]);


        return $this->Make_Response('comment created successfully',201,[
            'comment'=>[
                'id'=>$comment->id,
                'post_id'=>$comment->post_id,
                'replay_to_id'=>$comment->replay_to_id,
                'comment'=>$comment->comment,
                'timestamp'=>$comment->created_at
            ]
        ]);
    }
}
