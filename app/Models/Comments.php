<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comments extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'user_id',
        'post_id',
        'replay_to_id',
        'comment'
    ];

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function post(){
        return $this->belongsTo(Posts::class,'post_id','id');
    }

    public function replay_parent(){
        return $this->belongsTo(Comments::class,'replay_to_id','id');
    }

    public function replay_child(){
        return $this->hasMany(Comments::class,'replay_to_id','id');
    }


}
