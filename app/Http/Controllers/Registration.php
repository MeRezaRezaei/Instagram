<?php

namespace App\Http\Controllers;

use App\Actions\Response;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class Registration extends Controller
{
    use Response;

    public function registration(Request $request){
        $Validate = Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:200|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
            'bio' => 'string|max:255',
            'last_name' => 'string|max:255',
            'username'=>'string|max:255',
        ],[
            'name.required'=>'name is required',
            'name.string'=>'name must be string',
            'name.max'=>'name max length is 255',
            'email.required'=>'email is required',
            'email.string'=>'email must be string',
            'email.max'=>'email max length is 200',
            'email.unique'=>'this email have been registered before',
            'password.required'=>'password is require',
            'password.string'=>'password must be string',
            'password.min'=>'password minimum length is 8',
            'password.confirmed'=>'password does not math confirmation',
            'bio.string'=>'bio must be string',
            'bio.max'=>'bio max length is 255',
            'last_name.string'=>'last name must be string',
            'username.string'=>'username must be string',
            'username.max'=>'username maximum length is 255'
        ]);
        if ($Validate->fails())
            return $this->Make_Error($Validate->errors(),400)
                ;


        $profile_pic_path = null;
        $message = false;


        $has_profile_pic = $request->hasFile('profile_pic');
        if($has_profile_pic){

            $profile_pic_extension = $request
                ->file('profile_pic')
                ->extension();
            if (
                $profile_pic_extension == 'png'
                || $profile_pic_extension == 'jpg'
                || $profile_pic_extension == 'jpeg'

            ){

                if (!$request->file('profile_pic')->isValid())
                    $message = 'profile_pic was not upload successfully'
                    ;

            }else
                $message = 'profile_pic extension '
                    .$profile_pic_extension
                    .' is not valid'
                ;

        }


        if ($message)
            return $this->Make_Error($message,400)
                ;


        if ($has_profile_pic)
            $profile_pic_path = $request->file('profile_pic')
                ->store('images')
            ;


        User::create(
            [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'last_name' => $request->last_name,
                'bio' => $request->bio,
                'profile_pic_path' => $profile_pic_path
            ]
        );


        return $this->Make_Response(
            'user created successfully',
            201
        );

    }

    public function login(Request $request){
        $Validate = Validator::make($request->all(),[
            'email' => 'required|string|email',
            'password' => 'required|string',
        ],[
            'email.required'=>'email address is required for login',
            'email.string'=>'email address must be string',
            'email.email'=>'this is not a valid email string',
            'password.required'=>'password is required for login',
            'password.string'=>'password must be sting',
        ]);
        if ($Validate->fails())
            return $this->Make_Error($Validate->errors(),400)
                ;


        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials))
            return $this->Make_Response('login was successful',200)
                ;

        return $this->Make_Error(
            'you did not register or wrong email or password',
            400
        );

    }
}
