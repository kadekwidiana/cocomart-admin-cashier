<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImageSliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('image_sliders')->insert([
            [
                'id' => 1,
                'image' => '/assets/images/sliders/1.jpg',
                'link' => '#',
                'index' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'image' => '/assets/images/sliders/2.jpg',
                'link' => '#',
                'index' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'image' => '/assets/images/sliders/3.jpg',
                'link' => '#',
                'index' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'image' => '/assets/images/sliders/4.jpg',
                'link' => '#',
                'index' => 4,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
