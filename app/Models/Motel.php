<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Motel extends Model
{
    use HasFactory;
    protected $guarded =  [] ;

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function rooms() {
        return $this->hasMany(Room::class);
    }
    public function room_types() {
        return $this->hasMany(RoomType::class);
    }
    public function motel_imgs() {
        return $this->hasMany(MotelImg::class);
    }
}
