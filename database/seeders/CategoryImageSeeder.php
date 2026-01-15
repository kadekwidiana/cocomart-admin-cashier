<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'id' => 1,
                'oxy_category_id' => '1000006',
                'image' => '/assets/images/category-default.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'oxy_category_id' => '1000005',
                'image' => '/assets/images/category-default.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'oxy_category_id' => '1000001',
                'image' => '/assets/images/category-default.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'oxy_category_id' => '1000004',
                'image' => '/assets/images/category-default.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'oxy_category_id' => '504404853360134223',
                'image' => '/assets/images/category-default.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'oxy_category_id' => '5044049151508790917',
                'image' => '/assets/images/category-default.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'oxy_category_id' => '1000008',
                'image' => '/assets/images/category-default.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'oxy_category_id' => '1000002',
                'image' => '/assets/images/category-default.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 9,
                'oxy_category_id' => '1000003',
                'image' => '/assets/images/category-default.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 10,
                'oxy_category_id' => '1000007',
                'image' => '/assets/images/category-default.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('category_images')->insert($data);
    }
}
