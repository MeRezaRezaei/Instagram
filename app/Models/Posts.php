<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Posts extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'user_id'
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function pics(){
        return $this->hasMany(Pictures::class,'post_id','id');
    }
    public function likes(){
        return $this->hasMany(Likes::class,'post_id','id');
    }

    public function post_likes_users(){
        //return $this->hasManyThrough(User::class,Likes::class,'post_id','id','id','user_id');
        return $this->belongsToMany(
            User::class,
            'likes',
            'post_id',
            'user_id',
            'id',
            'id'
        )->as('comments');
    }

    public function comments(){
        return $this->hasMany(Comments::class,'post_id','id');
    }

    public function post_comments_users(){
        return $this->belongsToMany(
            User::class,
            'comments',
            'user_id',
            'post_id',
            'id',
            'id'
        )->as('comments');
    }

}
