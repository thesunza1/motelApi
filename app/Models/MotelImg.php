<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MotelImg extends Model
{
    use HasFactory;

    public function img_type() {
        return $this->belongsTo(ImgType::class);
    }
    public function  img_details() {
        return $this->hasMany(ImgDetail::class);
    }
}
