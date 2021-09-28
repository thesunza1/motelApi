<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    use HasFactory;
    protected $guarded =  [] ;


    public function posts() {
        return $this->hasMany(Post::class);
    }

    public function img_details() {
        return $this->hasMany(ImgDetail::class);
    }
    public function first_img_detail() {
       return $this->hasMany(ImgDetail::class)->latest();
    }

    public function rooms() {
        return $this->hasMany(Room::class);
    }
    public function had_rooms() {
        return $this->hasMany(Room::class)->where('room_status_id' , 2 );
    }
    public function none_rooms() {
        return $this->hasMany(Room::class)->where('room_status_id' , 1 );
    }
    public function motel() {
        return $this->belongsTo(Motel::class);
    }
}
