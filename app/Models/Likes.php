<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Likes extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'post_id'
    ];

    protected $primaryKey = 'post_id';
    public $incrementing = false;

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function post(){
        return $this->belongsTo(Posts::class,'post_id','id');
    }
}
