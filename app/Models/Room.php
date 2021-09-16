<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Room extends Model
{
    use HasFactory;
    protected $guarded =  [] ;

    public function posts() {
        return $this->hasMany(Post::class);
    }

    public function tenants() {
        return $this->hasMany(Tenant::class);
    }

    public function latest_tenant() {
        return $this->hasOne(Tenant::class)->latest();
    }

    public function noti_rooms() {
        return $this->hasMany(Noti::class);
    }

    public function room_status() {
        return $this->BelongsTo(RoomStatus::class);
    }

    public function room_type() {
        return $this->belongsTo(RoomType::class);
    }


}
