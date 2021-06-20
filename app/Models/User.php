<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function posts(){
        return $this->hasMany(Posts::class,'user_id','id');
    }


    public function following(){
        return $this->hasMany(Follows::class,'following_id','id');
    }
    public function followers(){
        return $this->hasMany(Follows::class,'follower_id','id');
    }

    public function sent_messages(){
        return $this->hasMany(Messages::class,'from_id','id');
    }

    public function received_messages(){
        return $this->hasMany(Messages::class,'to_id','id');
    }


    public function story(){
        return $this->hasMany(Story::class,'user_id','id');
    }


    public function likes(){
        return $this->hasMany(Likes::class,'user_id','id');
    }


    public function user_likes_posts(){
        return $this->hasManyThrough(Posts::class,Likes::class,'user_id','id','id','post_id');
    }
}
