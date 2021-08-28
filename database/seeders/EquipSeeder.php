<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Equip;

class EquipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Equip::insert([
            'name' => 'tủ lạnh',
            'type' => 0
        ]);
        Equip::insert([
            'name' => 'máy lạnh',
            'type' => 0
        ]);
        Equip::insert([
            'name' => 'đều hòa',
            'type' => 0
        ]);
        Equip::insert([
            'name' => 'máy giặc',
            'type' => 1
        ]);
        Equip::insert([
            'name' => 'máy rửa chén',
            'type' => 1
        ]);
    }
}
