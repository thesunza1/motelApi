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

    public function motel() {
        return $this->belongsTo(Motel::class);
    }
}
