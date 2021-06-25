<?php

namespace App\Http\Controllers;

use App\Models\Comments;
use App\Models\Follows;
use App\Models\Likes;
use App\Models\Messages;
use App\Models\Pictures;
use App\Models\Posts;
use App\Models\Story;
use App\Models\User;
use http\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;


class API extends Controller
{

    protected $LoggedInUserId = 1;
    public function toJSON($D){
        return json_encode($D,JSON_PRETTY_PRINT);
    }

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
        if ($Validate->fails()){
            return response($this->toJSON([
                '_'=>'error',
                'error'=>$Validate->errors()
            ]),400);
        }
        $profile_pic_path = null;
        $message = false;
        $has_profile_pic = $request->hasFile('profile_pic');
        if($has_profile_pic){
            $profile_pic_extension = $request->file('profile_pic')->extension();
            if (
                 $profile_pic_extension == 'png'
                || $profile_pic_extension == 'jpg'
                || $profile_pic_extension == 'jpeg'

            ){
                if (!$request->file('profile_pic')->isValid()){
                    $message = 'profile_pic was not upload successfully';
                }

            }else{
                $message = 'profile_pic extension '.$profile_pic_extension.' is not valid';
            }
        }
        if ($message){
            return response($this->toJSON([
                '_'=>'error',
                'error'=>[
                    'code'=>400,
                    'status'=>'fail',
                    'message'=> $message
                ]
            ]),400);
        }
        if ($has_profile_pic){
            $profile_pic_path = $request->file('profile_pic')->store('images');
        }
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'last_name' => $request->last_name,
            'bio' => $request->bio,
            'profile_pic_path' => $profile_pic_path

        ]);
        return response($this->toJSON([
            '_'=>'response',
            'response'=>[
                'code'=>201,
                'status'=>'success',
                'message'=> 'user created successfully'
            ]
        ]),201);

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
        if ($Validate->fails()){
            return response($this->toJSON([
                '_'=>'error',
                'error'=>$Validate->errors()
            ]),400);
        }
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return response($this->toJSON([
                '_'=>'response',
                'response'=>[
                    'code'=>200,
                    'status'=>'successful',
                    'message'=>'login was successful'
                ]
            ]),200);
        }
        return response($this->toJSON([
            '_'=>'error',
            'error'=>[
                'code'=>22,
                'status'=>'fail',
                'message'=>'you did not register or wrong email or password'
            ]
        ]),400);
    }

    public function new_post(Request $request){
        $validator = Validator::make($request->all(), [
            'description'=>'required|string|max:100',
        ],[
            'description.required'=>'description is required',
            'description.string' => 'description must be string',
            'description.max' => 'max allowed description is 100 characters',
        ]);
        if ($validator->fails()) {
            return response($this->toJSON([
                '_'=>'error',
                'response'=>['code'=>400,'status'=>'fail'],
                'errors'=> $validator->errors()
            ]),400) ;
        }
        $files_count = count($request->allFiles());
        if ($files_count == 0){
            return response($this->toJSON([
                '_'=>'error',
                'response'=>['code'=>400,'status'=>'fail'],
                'errors'=> 'posts must have at least one picture'
            ]),400) ;
        }
        elseif ($files_count > 10){
            return response($this->toJSON([
                '_'=>'error',
                'response'=>['code'=>400,'status'=>'fail'],
                'errors'=> 'posts can not have more than ten pictures'
            ]),400) ;
        }
        if (($message = $this->ValidatePicture($request))!= false){
            return response($this->toJSON([
                '_'=>'error',
                'response'=>['code'=>400,'status'=>'fail'],
                'errors'=> [
                    'invalid file' => $message
                ]
            ]),400) ;
        }
        ;
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
        return $this->toJSON([
            '_'=>'response',
            'response'=>['code'=>201,'status'=>'success']
        ]);
    }

    public function new_story(Request $request){
        $story_pic_path = null;
        $message = null;
        if ($request->hasFile('story_pic')){
            if($request->file('story_pic')->isValid()){
                $story_pic_extension = $request->file('story_pic')->extension();
                if (
                    $story_pic_extension == 'png'
                    || $story_pic_extension == 'jpg'
                    || $story_pic_extension == 'jpeg'
                ){
                    $story_pic_path = $request->file('story_pic')->store('images');
                }
            }
            else{
                $message = 'story_pic file was not upload successfully';
            }
        }
        else{
            $message = 'story_pic file does not exist';
        }
        if ($message){
            return response($this->toJSON([
                '_'=>'error',
                'error'=>[
                    'code'=>400,
                    'status'=>'fail',
                    'message'=> $message
                ]
            ]),400);
        }
        $story = new Story();
        $story->path = $story_pic_path;
        $story->user_id = $this->LoggedInUserId;
        $story->save();
        return response($this->toJSON([
            '_'=>'response',
            'response'=>[
                'code'=>201,
                'status'=>'success',
                'message'=>'story stored successfully'
            ]
        ]),201);
    }

    public function send_dm(Request $request){
        $Validate = Validator::make($request->all(),[
            'message'=>'required|string|max:255',
            'peer_id'=>'required|integer|exists:App\Models\User,id',
        ],[
            'message.required'=>'message can not be empty',
            'message.string'=>'message must be string',
            'message.max'=>'message max length is 255',
            'peer_id.required'=>'peer_id can not be empty',
            'peer_id.integer'=>'peer_id must be integer'
        ]);
        if ($Validate->fails()){
            return response($this->toJSON([
                '_'=>'error',
                'response'=>['code'=>400,'status'=>'fail'],
                'errors'=> $Validate->errors()
            ]),400) ;
        }
        $peer = User::find($request->peer_id);
        if ($peer == null){
            return response($this->toJSON([
                '_'=>'error',
                'errors'=> [
                    'code'=>400,
                    'status'=>'fail',
                    'message'=>'could not find peer_id'
                ]
            ]),400) ;
        }
        $message = $peer->received_messages()->create([
            'from_id'=> $this->LoggedInUserId,
            'text'=> $request->message,
            'is_read'=>false
        ]);
        return response($this->toJSON([
            '_'=>'response',
            'response'=> [
                'code'=>201,
                'status'=>'success',
                'message'=>'message sent successfully',
                'message_id'=> $message->id
            ]
        ]),201) ;
    }


    public function get_user_profile(){
        $user = User::find($this->LoggedInUserId);
        if ($user == null){
            return response('something went wrong',500);
        }
        $user_profile = [
          '_'=>'user_profile',
            'name'=>$user->name,
            'last_name'=>$user->last_name,
            'email'=>$user->email,
            'bio' =>$user->bio,
            'id' =>$user->id,
            'profile_pic_path' =>$user->profile_pic_path,
        ];
        return response($this->toJSON([
            '_'=>'response',
            'response' =>[
                'code' =>200,
                'status'=>'success',
                'message' =>'logged in user profile information'
            ],
            'profile'=>$user_profile
        ]),200);
    }
    public function set_user_profile(Request $request){
        if (!$request->has('_') && !($request->get('_') == 'user_profile')){
            return response($this->toJSON([
                '_'=>'error',
                'error'=>[
                    'code' =>400,
                    'status'=>'fail',
                    'message' =>'profile predicate \'_\' was not found',
                ]
            ]),400);
        }
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
        if ($Validate->fails()){
            return response($this->toJSON([
                '_'=>'error',
                'error'=>[
                    'code' =>400,
                    'status'=>'fail',
                    'message' =>$Validate->errors(),
                ]
            ]),400);
        }
        $omit_profile_pic = false;
        if($request->has('profile_pic') && !$request->hasFile('profile_pic')){
            $profile_pic = $request->input('profile_pic');
            if (
                    $profile_pic == 'false'
                || $profile_pic == 'null'
                || $profile_pic == ''
            ){
                $omit_profile_pic = true;
            }
            else{
                return response($this->toJSON([
                    '_'=>'error',
                    'error'=>[
                        'code'=>400,
                        'status'=>'fail',
                        'message'=>'valid argument for deleting profile_pic is \'false\',\'null\',\'\' no valid argument or file have been sent'
                    ]
                ]),400);
            }
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
                    if (!$request->file('profile_pic')->isValid()){
                        $message = 'profile_pic was not upload successfully';
                    }

                }else{
                    $message = 'profile_pic extension '.$profile_pic_extension.' is not valid';
                }
            }
            if ($message){
                return response($this->toJSON([
                    '_'=>'error',
                    'error'=>[
                        'code'=>400,
                        'status'=>'fail',
                        'message'=> $message
                    ]
                ]),400);
            }
            if ($has_profile_pic){
                $profile_pic_path = $request->file('profile_pic')->store('images');
            }
        }


        $user = User::find($this->LoggedInUserId);
        if ($user == null){
            return response('something went wrong',500);
        }

        if ($omit_profile_pic){
            if(Storage::exists($user->profile_pic_path)) {
                Storage::delete($user->profile_pic_path);
                $user->profile_pic_path = null;
            }
        }
        $user->name = $request->name ? $request->name: $user->name;
        $user->email = $request->email ? $request->email: $user->email;
        if ($request->has('bio')){
            if ($request->bio !== ""){
                $user->bio = $request->bio;
            }
            else{
                $user->bio = null;
            }
        }
        if ($request->has('last_name')){
            if ($request->last_name !== ""){
                $user->last_name = $request->last_name;
            }
            else{
                $user->last_name = null;
            }
        }
        if ($has_profile_pic){
            $user->profile_pic_path = $profile_pic_path;
        }
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
        return response($this->toJSON([
            '_'=>'response',
            'response'=>[
                'code'=>201,
                'status'=>'success',
                'message'=>'new user profile'
            ],
            'profile'=> $user_profile
        ]),201);
    }


    public function send_comment(Request $request){
        $Validate = Validator::make($request->all(),[
            'post_id'=>'required|integer|exists:App\Models\Posts,id',
            'comment'=>'required|string|max:255',
            'replay_to'=>'integer|exists:App\Models\Comments,id'
        ],[

        ]);
        if ($Validate->fails()){
            return response($this->toJSON([
                '_'=>'error',
                'error'=>[
                    'code'=>400,
                    'status'=>'success',
                    'message'=> 'validation failed'
                ],
                'errors'=>$Validate->errors()
            ]),400);
        }
        $post = Posts::where('id',$request->post_id)->first();
        if ($post === null){
            return response($this->toJSON([
                '_'=>'error',
                'error'=>[
                    'code'=>400,
                    'status'=>'success',
                    'message'=> 'this post does not exist or soft deleted'
                ],
            ]),400);
        }
        $replay_to = $request->replay_to;
        $comment = Comments::where('id',$replay_to)->first();
        if ($comment === null){
            return response($this->toJSON([
                '_'=>'error',
                'error'=>[
                    'code'=>400,
                    'status'=>'success',
                    'message'=> 'this comment does not exist or soft deleted'
                ],
            ]),400);
        }
        if($post->id !== $comment->post_id){
            return response($this->toJSON([
                '_'=>'error',
                'error'=>[
                    'code'=>400,
                    'status'=>'success',
                    'message'=> 'validation failed'
                ],
                'errors'=>[
                    'replay_to'=>'the comment you want to replay on does not belong to selected post'
                ]
            ]),400);
        }
        $comment = $post->comments()->create([
            'replay_to_id'=>$request->replay_to,
            'user_id'=>$this->LoggedInUserId,
            'comment'=>$request->comment
        ]);
        return response($this->toJSON([
            '_'=>'response',
            'response'=>[
                'code'=>201,
                'status'=>'success',
                'message'=>'comment created successfully'
            ],
            'comment'=>[
                'id'=>$comment->id,
                'post_id'=>$comment->post_id,
                'replay_to_id'=>$comment->replay_to_id,
                'comment'=>$comment->comment,
                'timestamp'=>$comment->created_at
            ]
        ]),201);
    }


    public function like(Request $request){
        $Validate = Validator::make($request->all(),[
            'post_id'=>'required|integer|exists:App\Models\Posts,id'
        ],[

        ]);
        if ($Validate->fails()){
            return response($this->toJSON([
                '_'=>'error',
                'error'=>[
                    'code'=>400,
                    'status'=>'success',
                    'message'=> 'validation failed'
                ],
                'errors'=>$Validate->errors()
            ]),400);
        }
        $post = Posts::where('id',$request->post_id)->first();
        if ($post === null){
            return response($this->toJSON([
                '_'=>'error',
                'error'=>[
                    'code'=>400,
                    'status'=>'success',
                    'message'=> 'this post does not exist or soft deleted'
                ],
                'errors'=>$Validate->errors()
            ]),400);
        }
        $like = Likes::firstOrCreate([
            'user_id'=>$this->LoggedInUserId,
            'post_id'=>$request->post_id
        ]);
        $post = $like->post;
        $user = $like->user;

        return response($this->toJSON([
            '_'=>'response',
            'response'=>[
                'code'=>201,
                'status'=>'success',
                'message'=>'post liked successfully'
            ],
            'post'=>[
                'id'=>$post->id,
                'user_id'=>$post->user_id,
                'description'=>$post->description
            ],
            'user'=>[
                'id'=>$user->id,
                'name'=>$user->name,
                'email'=>$user->email,
                'bio'=>$user->bio,
                'last_name'=>$user->last_name
            ],
        ]),201);
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

    protected function fetch_comment($comment_id){
        $comment = Comments::find($comment_id);
        if ($comment_id === null){
            return null;
        }
        $comment_child_count = $comment->replay_child()->count();
        if ($comment_child_count === 0){
            return null;
        }
        $comment_children = [];
        foreach ($comment->replay_child()->where('post_id',$comment->post_id)->get() as $child){
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

    public function get_post(Request $request){
        $Validate = Validator::make($request->all(),[
            'post_id'=>'required|integer|exists:App\Models\Posts,id'
        ],[

        ]);
        if ($Validate->fails()){
            return response($this->toJSON([
                '_'=>'error',
                'error'=>[
                    'code'=>400,
                    'status'=>'success',
                    'message'=> 'validation failed'
                ],
                'errors'=>$Validate->errors()
            ]),400);
        }
        $post = Posts::where('id',$request->post_id)->first();
        if ($post === null){
            return response($this->toJSON([
                '_'=>'error',
                'error'=>[
                    'code'=>400,
                    'status'=>'success',
                    'message'=> 'this post does not exist or soft deleted'
                ],
                'errors'=>$Validate->errors()
            ]),400);
        }
        $post_full = $this->fetch_Post($request->psot_id);
        return response($this->toJSON([
            '_'=>'response',
            'response'=>[
                'code'=>200,
                'status'=>'success',
                'message'=>'full post information'
            ],
            'post'=>$post_full
        ]),200);
    }

    public function follow(Request $request){
        $Validate = Validator::make($request->all(),[
            'user_id'=>'required|integer|exists:App\Models\User,id'
        ]);
        if ($Validate->fails()){
            return response($this->toJSON([
                '_'=>'error',
                'error'=>[
                    'code'=>400,
                    'status'=>'success',
                    'message'=> 'validation failed'
                ],
                'errors'=>$Validate->errors()
            ]),400);
        }
        $follow = Follows::firstOrCreate([
            'following_id'=>$request->user_id,
            'follower_id'=>$this->LoggedInUserId
        ]);
        $following = $follow->following;
        $follower = $follow->follower;
        return response($this->toJSON([
            '_'=>'response',
            'response'=>[
                'code'=>201,
                'status'=>'success',
                'message'=>'user successfully followed'
            ],
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
        ]),201);
    }
    public function unfollow(Request $request){
        $Validate = Validator::make($request->all(),[
            'user_id'=>'required|integer|exists:App\Models\User,id'
        ]);
        if ($Validate->fails()){
            return response($this->toJSON([
                '_'=>'error',
                'error'=>[
                    'code'=>400,
                    'status'=>'success',
                    'message'=> 'validation failed'
                ],
                'errors'=>$Validate->errors()
            ]),400);
        }

        $follow = DB::table('follows')
            ->where([
                ['following_id','=',$request->user_id],
                ['follower_id','=',$this->LoggedInUserId]
            ])->delete();
        return response($this->toJSON([
            '_'=>'response',
            'response'=>[
                'code'=>201,
                'status'=>'success',
                'message'=>'user successfully unfollowed'
            ],

        ]),201);
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
        return response($this->toJSON([
            '_'=>'response',
            'response'=>[
                'code'=>200,
                'status'=>'success',
                'message'=>'post feed'
            ],
            'posts'=>$feed_posts
        ]),200);
    }
    public function get_profile_posts(){
        $user = User::find($this->LoggedInUserId);
        $posts = $user->posts;
        $self_posts = [];
        foreach ($posts as $post){
            $self_posts[] = $this->fetch_Post($post->id);
        }
        return response($this->toJSON([
            '_'=>'response',
            'response'=>[
                'code'=>200,
                'status'=>'success',
                'message'=>'all of your profile posts'
            ],
            'posts'=>$self_posts
        ]),200);
    }


    public function get_dialog(Request $request){
        $Validate = Validator::make($request->all(),[
            'peer_id'=>'required|integer|exists:App\Models\User,id'
        ],[]);
        if ($Validate->fails()){
            return response($this->toJSON([
                '_'=>'error',
                'error'=>[
                    'code'=>400,
                    'status'=>'success',
                    'message'=> 'validation failed'
                ],
                'errors'=>$Validate->errors()
            ]),400);
        }
        DB::table('messages')->where([
        ['from_id','=',$request->peer_id],
            ['to_id','=',$this->LoggedInUserId]
        ])->update(['is_read'=>true]);
        $dialogs = Messages::where([
            ['from_id','=',$this->LoggedInUserId],
            ['to_id','=',$request->peer_id]
        ])->orWhere([
            ['from_id','=',$request->peer_id],
            ['to_id','=',$this->LoggedInUserId]
        ])->get();
        $messages = [];
        foreach ($dialogs as $dialog){
            $messages[] = [
                'id'=>$dialog->id,
                'message'=>$dialog->text,
                'created_at'=>$dialog->created_at,
                'updated_at'=>$dialog->updated_at,
                'is_read'=>$dialog->is_read,
                'from'=>[
                    'id'=>$dialog->from->id,
                    'name'=>$dialog->from->name,
                    'last_name'=>$dialog->from->last_name,
                    'email'=>$dialog->from->email
                ],
                'to'=>[
                    'id'=>$dialog->to->id,
                    'name'=>$dialog->to->name,
                    'last_name'=>$dialog->to->last_name,
                    'email'=>$dialog->to->email
                ]
            ];
        }
        return response($this->toJSON([
            '_'=>'response',
            'response'=>[
                'code'=>200,
                'status'=>'success',
                'message'=>'dialog messages'
            ],
            'dialog'=>$messages
        ]),200);
    }

    public function get_story_feed(){
        $user = User::find($this->LoggedInUserId);
        $following_users = $user->following;
        $feed_stories = [];
        foreach ($following_users as $following_user){
            $stories = $following_user->story;
            foreach ($stories as $story){
                $feed_stories[] = [
                    'id'=>$story->id,
                    'path'=>$story->path,
                    'owner_id'=>$following_user->id,
            ];
            }
        }
        return response($this->toJSON([
            '_'=>'response',
            'response'=>[
                'code'=>200,
                'status'=>'success',
                'message'=>'user story feed'
            ],
            'stories'=>$feed_stories
        ]),200);
    }
    public function delete_post(Request $request){
        $Validate = Validator::make($request->all(),[
            'post_id'=>'required|integer'
        ]);
        if ($Validate->fails()){
            return response($this->toJSON([
                '_'=>'error',
                'error'=>[
                    'code'=>400,
                    'status'=>'fail',
                    'message'=> 'validation failed'
                ],
                'errors'=>$Validate->errors()
            ]),400);
        }
        $message = '';
        $user = User::find($this->LoggedInUserId);
        $status_post = '';
        $trashed_post = Posts::where('id','=',$request->post_id)->withTrashed()->first();
        if ($trashed_post === null){
            $message = 'this post does not exist or permanently deleted';
            goto permanently_Deleted;
        }
        else{

            if ($trashed_post->user_id != $user->id){
                return response($this->toJSON([
                    '_'=>'error',
                    'error'=>[
                        'code'=>400,
                        'status'=>'fail',
                        'message'=> 'validation failed'
                    ],
                    'errors'=>'this post does not belongs to you and you dont have the right to delete it.'
                ]),400);
            }
            if ($trashed_post->deleted_at === null){
                $trashed_post->delete();
                $message = 'post soft deleted successfully';

            }
            else{
                $message = 'this post have been soft deleted before';
            }
        }

        permanently_Deleted:
        return response($this->toJSON([
            '_'=> 'response',
            'response'=>[
                'code'=>200,
                'status'=>'success',
                'message'=>$message,
            ]
        ]),200);

    }

    public function delete_comment(Request $request){
        $Validate = Validator::make($request->all(),[
            'comment_id'=>'required|integer'
        ]);
        if ($Validate->fails()){
            return response($this->toJSON([
                '_'=>'error',
                'error'=>[
                    'code'=>400,
                    'status'=>'fail',
                    'message'=> 'validation failed'
                ],
                'errors'=>$Validate->errors()
            ]),400);
        }
        $message = '';
        $user = User::find($this->LoggedInUserId);
        $trashed_comment = Comments::where('id',$request->comment_id)->withTrashed()->first();
        if ($trashed_comment === null){
            $message = 'this comment does not exist or deleted before';
            goto Permanently_Deleted;
        }
        else{
            if ($trashed_comment->user_id != $user->id ){
                return response($this->toJSON([
                    '_'=>'error',
                    'error'=>[
                        'code'=>400,
                        'status'=>'fail',
                        'message'=> 'validation failed'
                    ],
                    'errors'=>'this comment does not belongs to you and you dont have the right to delete it.'
                ]),400);
            }
            if ($trashed_comment->deleted_at === null){
                $trashed_comment->delete();
                $message = 'comment deleted successfully';
            }
            else{
                $message = 'this comment have been soft deleted before';
            }
        }
        Permanently_Deleted:
        return response($this->toJSON([
            '_'=>'response',
            'response'=>[
                'code'=>200,
                'status'=>'success',
                'message'=> $message
            ],

        ]),200);

    }
    public function get_post_comments(Request $request){
        $Validate  = Validator::make($request->all(),[
            'post_id'=>'required|integer'
        ]);
        if ($Validate->fails()){
            return response($this->toJSON([
                '_'=>'error',
                'error'=>[
                    'code'=>400,
                    'status'=>'fail',
                    'message'=> 'validation failed'
                ],
                'errors'=>$Validate->errors()
            ]),400);
        }
        $post = Posts::find(1);
        if ($post === null){
            return response($this->toJSON([
                '_'=>'error',
                'error'=>[
                    'code'=>400,
                    'status'=>'fail',
                    'message'=> 'this post does not exist or deleted'
                ],
            ]),400);
        }
        $post_comments = $post->comments()->where('post_id',$post->id)
            ->whereNull('replay_to_id')->get();
        $comments = [];
        foreach ($post_comments as $post_comment){
            $comments[] =[
                'id'=>$post_comment->id,
                'user_id'=>$post_comment->user_id,
                'post_id'=>$post_comment->post_id,
                'comment'=>$post_comment->comment,
                'child_replay'=>$this->fetch_comment($post_comment->id)
            ];
        }
        return response($this->toJSON([
            '_'=>'response',
            'response'=>[
                'code'=>200,
                'status'=>'success',
                'message'=> 'all comments for this post'
            ],
            'comments'=>$comments
        ]),200);
    }
    public function get_following_lists(){
        $user = User::find($this->LoggedInUserId);
        $following = $user->following;
        $follow_list = [];
        foreach ($following as $item){
            $follow_list[] = [
                'id'=>$item->id,
                'name'=>$item->name,
                'last_name'=>$item->last_name,
                'email'=>$item->email,
            ];
        }
        return response($this->toJSON([
            '_'=>'response',
            'response'=>[
                'code'=>200,
                'status'=>'success',
                'message'=> 'all the users who this person follow them'
            ],
            'following'=>$follow_list
        ]),200);
    }
    public function get_follower_lists(){
        $user = User::find($this->LoggedInUserId);
        $followers = $user->followers;
        $follow_list = [];
        foreach ($followers as $item){
            $follow_list[] = [
                'id'=>$item->id,
                'name'=>$item->name,
                'last_name'=>$item->last_name,
                'email'=>$item->email,
            ];
        }
        return response($this->toJSON([
            '_'=>'response',
            'response'=>[
                'code'=>200,
                'status'=>'success',
                'message'=> 'all the users who followed this person'
            ],
            'following'=>$follow_list
        ]),200);
    }

}
