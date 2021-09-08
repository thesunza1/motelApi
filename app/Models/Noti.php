<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Noti extends Model
{
    use HasFactory;
    protected $guarded =  [] ;

    public function room() {
        return $this->belongsTo(Room::class);
    }
    public function senderUser() {
        return $this->belongsTo(User::class, 'sender_id');
    }
    public function receiverUser() {
        return $this->belongsTo(User::class, 'receiver_id');
    }

}
