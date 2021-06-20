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
}
