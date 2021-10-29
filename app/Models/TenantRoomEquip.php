<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantRoomEquip extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function tenant() {
        return $this->belongsTo(Tenant::class);
    }

    public function img_details() {
        return $this->hasMany(ImgDetail::class);
    }
}
