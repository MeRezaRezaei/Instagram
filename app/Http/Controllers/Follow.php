<?php

namespace App\Http\Controllers;

use App\Actions\Response;
use App\Models\Follows;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Follow extends Controller
{
    use Response;

    public function follow(Request $request){
        $Validate = Validator::make($request->all(),[
            'user_id'=>'required|integer|exists:App\Models\User,id'
        ]);

        if ($Validate->fails())
            return $this->Make_Error($Validate->errors(),400)
                ;

        if ($request->user_id == $this->LoggedInUserId){
            return $this->Make_Error('you can not follow yourself',400);
        }

        $follow = Follows::firstOrCreate([
            'following_id'=>$request->user_id,
            'follower_id'=>$this->LoggedInUserId
        ]);


        $following = $follow->following;
        $follower = $follow->follower;


        return $this->Make_Response(
            'user successfully followed',
            201,[
            'following'=>[
                'id'=>$following->id,
                'name'=>$following->name,
                'email'=>$following->email,
                'last_name'=>$following->last_name
            ],
            'follower'=>[
                'id'=>$follower->id,
                'name'=>$follower->name,
                'email'=>$follower->email,
                'last_name'=>$follower->last_name
            ]
        ]);

    }

    public function unfollow(Request $request){
        $Validate = Validator::make($request->all(),[
            'user_id'=>'required|integer|exists:App\Models\User,id'
        ]);
        if ($Validate->fails())
            return $this->Make_Error($Validate->errors(),400)
                ;


        DB::table('follows')
        ->where([
            ['following_id','=',$request->user_id],
            ['follower_id','=',$this->LoggedInUserId]
        ])
        ->delete();


        return $this->Make_Response(
            'user successfully unfollowed',
            201
        );
    }



    public function get_following_lists(){
        $user = User::find($this->LoggedInUserId);
        $following = $user->following;


        $follow_list = [];
        foreach ($following as $item)
            $follow_list[] = [
                'id'=>$item->id,
                'name'=>$item->name,
                'last_name'=>$item->last_name,
                'email'=>$item->email,
            ]
            ;


        return $this->Make_Response(
            'all the users who this person follow them',
            200,
            $follow_list
        );

    }


    public function get_follower_lists(){
        $user = User::find($this->LoggedInUserId);
        $followers = $user->followers;


        $follow_list = [];
        foreach ($followers as $item)
            $follow_list[] = [
                'id'=>$item->id,
                'name'=>$item->name,
                'last_name'=>$item->last_name,
                'email'=>$item->email,
            ]
            ;


        return $this->Make_Response(
            'all the users who followed this person',
            200,
            $follow_list
        );

    }
}
