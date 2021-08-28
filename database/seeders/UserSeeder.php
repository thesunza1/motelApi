<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        User::insert([
            'name' => 'user test01',
            'email' => 'test01@gmail.com',
            'password' => Hash::make('test01'),
            'address' => 'ninh kieu , can tho',
            'role_id' => 1,
            'sex' => 0,
            'birth_date' => Carbon::now(),
            'phone_number' => '0123653789',
            'job' => 'tai chinh ngan hang ' ,
            'updated_at' => Carbon::now(),
            'created_at' => Carbon::now(),
        ]);
        User::insert([
            'name' => 'motel moteltest01',
            'email' => 'moteltest01@gmail.com',
            'password' => Hash::make('moteltest01'),
            'address' => 'ninh kieu , can tho',
            'role_id' => 2,
            'sex' => 1,
            'birth_date' => Carbon::now(),
            'phone_number' => '0123653789',
            'job' => 'chu tro dang dang ' ,
            'updated_at' => Carbon::now(),
            'created_at' => Carbon::now(),
        ]);
        User::insert([
            'name' => 'admin admin01',
            'email' => 'admin01@gmail.com',
            'password' => Hash::make('admin01'),
            'address' => 'ninh kieu , can tho',
            'role_id' => 3,
            'sex' => 0,
            'birth_date' => Carbon::now(),
            'phone_number' => '0123653789',
            'job' => 'chu du an ne' ,
            'updated_at' => Carbon::now(),
            'created_at' => Carbon::now(),
        ]);
    }

}
