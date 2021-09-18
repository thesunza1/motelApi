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
        return $this->hasMany(Bill::class)->orderByDesc('created_at');
    }
    public function num_bills() {
        return $this->hasMany(Bill::class);
    }
    public function latest_bill() {
        return $this->hasOne(Bill::class)->latest();
    }
    public function no_bills() {
        return $this->hasMany(Bill::class)->where('status', 0);
    }
    public function tenant_room_equips() {
        return $this->hasMany(TenantRoomEquip::class);
    }
    public function room() {
        return $this->belongsTo(Room::class);
    }
}

