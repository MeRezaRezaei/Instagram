<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Follows extends Model
{
    use HasFactory;
    protected $fillable = [
        'following_id',
        'follower_id'
    ];

    protected $primaryKey = 'following_id';
    public $incrementing = false;

    public function following(){
        return $this->belongsTo(User::class,'following_id','id');
    }
    public function follower(){
        return $this->belongsTo(User::class,'follower_id','id');
    }
}
