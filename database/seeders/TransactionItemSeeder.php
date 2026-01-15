<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'id' => 1,
                'transaction_id' => 1,
                'oxy_item_master_id' => '5044049073590832763',
                'quantity' => 2,
                'price' => 15000,
                'subtotal' => 30000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'transaction_id' => 1,
                'oxy_item_master_id' => '5044049037451419891',
                'quantity' => 1,
                'price' => 20000,
                'subtotal' => 20000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'transaction_id' => 2,
                'oxy_item_master_id' => '5044049073590832763',
                'quantity' => 3,
                'price' => 15000,
                'subtotal' => 45000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'transaction_id' => 2,
                'oxy_item_master_id' => '5044049037451419891',
                'quantity' => 2,
                'price' => 20000,
                'subtotal' => 40000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('transaction_items')->insert($items);
    }
}
