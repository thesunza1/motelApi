<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;
    protected $guarded =[];

    public function tenant_users() {
        return $this->hasMany(TenantUser::class);
    }
    public function bills() {
        return $this->hasMany(Bill::class);
    }
    public function tenant_room_equips() {
        return $this->hasMany(TenantRoomEquip::class);
    }
    public function room() {
        return $this->belongsTo(Room::class);
    }
}

