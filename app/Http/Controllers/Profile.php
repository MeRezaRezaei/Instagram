<?php

namespace App\Http\Controllers;

use App\Actions\Response;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class Profile extends Controller
{
    use Response;

    public function get_user_profile(){
        $user = User::find($this->LoggedInUserId);
        if ($user == null)
            return $this->Make_Error('something went wrong',500)
                ;


        $user_profile = [
            '_'=>'user_profile',
            'name'=>$user->name,
            'last_name'=>$user->last_name,
            'email'=>$user->email,
            'bio' =>$user->bio,
            'id' =>$user->id,
            'profile_pic_path' =>$user->profile_pic_path,
        ];


        return $this->Make_Response(
            'logged in user profile information',
            200,$user_profile
        );

    }
    public function set_user_profile(Request $request){
        if (
            !$request->has('_')
            && !($request->get('_') == 'user_profile')
        )return $this->Make_Error(
            'profile predicate \'_\' was not found',400)
            ;

        $Validate = Validator::make($request->all(),[
            'name' => 'string|max:255',
            'email' => 'string|email|max:200|unique:users',
            'bio' => 'max:255',
            'last_name' => 'max:255',
            'username'=>'max:255',
        ],[
            'name.string'=>'name must be string',
            'name.max'=>'name max length is 255',
            'email.string'=>'email must be string',
            'email.max'=>'email max length is 200',
            'email.unique'=>'this email have been registered before',
            'bio.string'=>'bio must be string',
            'bio.max'=>'bio max length is 255',
            'last_name.string'=>'last name must be string',
            'username.string'=>'username must be string',
            'username.max'=>'username maximum length is 255'
        ]);
        if ($Validate->fails())
            return $this->Make_Error($Validate->errors(),400)
                ;


        $omit_profile_pic = false;
        if(
            $request->has('profile_pic')
            && !$request->hasFile('profile_pic')
        ){
            $profile_pic = $request->input('profile_pic');
            if (
                $profile_pic == 'false'
                || $profile_pic == 'null'
                || $profile_pic == ''
            )
                $omit_profile_pic = true
                ;

            else
                return $this->Make_Error(
                    'valid argument for deleting profile_pic is \'false\',\'null\',\'\' no valid argument or file have been sent',
                    400)
                    ;

        }



        $profile_pic_path = null;
        $message = false;
        $has_profile_pic = $request->hasFile('profile_pic');
        if (!$omit_profile_pic){


            if($has_profile_pic){
                $profile_pic_extension = $request->file('profile_pic')->extension();
                if (
                    $profile_pic_extension == 'png'
                    || $profile_pic_extension == 'jpg'
                    || $profile_pic_extension == 'jpeg'

                ){
                    if (!$request->file('profile_pic')->isValid())
                        $message = 'profile_pic was not upload successfully'
                        ;
                }else
                    $message =
                        'profile_pic extension '
                        .$profile_pic_extension
                        .' is not valid'
                    ;
            }


            if ($message)
                return $this->Make_Error($message,400)
                    ;

            if ($has_profile_pic)
                $profile_pic_path =
                    $request->file('profile_pic')
                        ->store('images')
                ;

        }


        $user = User::find($this->LoggedInUserId);
        if ($user == null)
            return $this->Make_Error('something went wrong',500)
                ;



        if (
            $omit_profile_pic
            && Storage::exists($user->profile_pic_path)
        ){
            Storage::delete($user->profile_pic_path);
            $user->profile_pic_path = null;
        }


        $user->name = $request->name ?: $user->name;
        $user->email = $request->email ?: $user->email;


        if ($request->has('bio'))
            if ($request->bio !== "")
                $user->bio = $request->bio
                ;
            else
                $user->bio = null
                ;


        if ($request->has('last_name'))
            if ($request->last_name !== "")
                $user->last_name = $request->last_name
                ;
            else
                $user->last_name = null
                ;


        if ($has_profile_pic)
            $user->profile_pic_path = $profile_pic_path
            ;
        $user->save();


        $user_profile = [
            '_'=>'user_profile',
            'name'=>$user->name,
            'last_name'=>$user->last_name,
            'email'=>$user->email,
            'bio' =>$user->bio,
            'id' =>$user->id,
            'profile_pic_path' =>$user->profile_pic_path,
        ];
        return $this->Make_Response(
            'new user profile',201,$user_profile);

    }
}
