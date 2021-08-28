<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NotiType;

class NotiTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        NotiType::insert(['name'=>'notification']);
        NotiType::insert(['name'=>'report']);
        NotiType::insert(['name'=>'invite']);
        NotiType::insert(['name'=>'confirm']);
    }
}
