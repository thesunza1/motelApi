<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RoomStatus;

class RoomStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        RoomStatus::insert(['name'=> 'none']);
        RoomStatus::insert(['name'=> 'had']);
        RoomStatus::insert(['name'=> 'disable']);

    }
}
