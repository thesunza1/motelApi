<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'address',
        'role_id',
        'sex',
        'birth_date',
        'phone_number' ,
        'job',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    //motel  one to one
    public function motel() {
        return $this->hasOne(Motel::class);
    }
    //role one to many invert
    public function role() {
        return $this->belongsTo(Role::class);
    }

    //post noti sender and receiver
    public function noti_senders() {
        return $this->hasMany(Noti::class,'sender_id');
    }
    public function noti_receivers() {
        return $this->hasMany(Noti::class,'receiver_id');
    }
    public function latest_tenant_user() {
        return $this->hasOne(TenantUser::class)->latest() ;
    }

}
