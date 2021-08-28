<?php

namespace Database\Seeders;

use App\Models\ImgType;
use App\Models\Role;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            ImgTypeSeeder::class,
            NotiTypeSeeder::class,
            PostTypeSeeder::class,
            RoomStatusSeeder::class,
            EquipSeeder::class,
        ]);
    }
}
