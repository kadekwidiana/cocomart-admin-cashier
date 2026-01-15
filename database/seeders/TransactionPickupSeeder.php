<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionPickupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('transaction_pickups')->insert([
            [
                'id' => 1,
                'transaction_id' => 1,
                'pickup_code' => 'PICKUP-0001',
                'pickup_time' => now()->addHours(2),
                'pickup_end_time' => now()->addHours(4),
                'receiver_name' => 'Kadek Widiana',
                'receiver_phone_number' => '081234567890',
                'status' => 'PENDING',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
