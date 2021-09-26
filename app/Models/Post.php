<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $guarded =  [] ;
    public function comments() {
        return $this->hasMany(Comment::class)->orderByDesc('created_at');
    }
    public function post_type() {
        return $this->belongsTo(PostType::class);
    }
    public function room() {
        return $this->belongsTo(Room::class);
    }
    public function room_type() {
        return $this->belongsTo(RoomType::class);
    }
}
