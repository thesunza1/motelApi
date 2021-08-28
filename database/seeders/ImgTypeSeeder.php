<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ImgType;

class ImgTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        ImgType::insert(['name'=>'public']);
        ImgType::insert(['name'=>'equip']);
    }
}
