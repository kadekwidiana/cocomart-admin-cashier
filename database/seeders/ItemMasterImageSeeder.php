<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemMasterImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['oxy_item_master_id' => '5044049073590832763'],
            ['oxy_item_master_id' => '5044049037451419891'],
            ['oxy_item_master_id' => '5044049037450876404'],
            ['oxy_item_master_id' => '5044049037455608687'],
            ['oxy_item_master_id' => '5044049037455519215'],
            ['oxy_item_master_id' => '5044049037455564295'],
            ['oxy_item_master_id' => '5044049037455549831'],
            ['oxy_item_master_id' => '5044049037455638962'],
            ['oxy_item_master_id' => '5044049037455579152'],
            ['oxy_item_master_id' => '5044049037455092080'],
            ['oxy_item_master_id' => '5044049037453104333'],
            ['oxy_item_master_id' => '5044049037453169021'],
            ['oxy_item_master_id' => '5044049037456927090'],
            ['oxy_item_master_id' => '5044049037456896396'],
            ['oxy_item_master_id' => '5044049037457123437'],
            ['oxy_item_master_id' => '5044049037456911504'],
            ['oxy_item_master_id' => '5044049037456865248'],
            ['oxy_item_master_id' => '5044049037456850663'],
            ['oxy_item_master_id' => '5044049037456942765'],
            ['oxy_item_master_id' => '5044049037456879725'],
        ];

        $id = 1;

        $payload = array_map(function ($item) use (&$id) {
            return [
                'id' => $id++,
                'oxy_item_master_id' => $item['oxy_item_master_id'],
                'image' => '/assets/images/product-default.png',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $data);

        DB::table('item_master_images')->insert($payload);
    }
}
