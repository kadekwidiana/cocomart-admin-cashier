<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('transactions')->insert([
            [
                'id' => 1,
                'oxy_customer_id' => '6277952820',
                'oxy_location_id' => '5044049223204002284',
                'status' => 'PENDING',
                'fulfillment_type' => 'PICKUP',
                'payment_token' => null,
                'subtotal' => 150000.00,
                'discount' => 10000.00,
                'shipping_cost' => 0,
                'total' => 140000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'oxy_customer_id' => '31230908051632942',
                'oxy_location_id' => '6660012',
                'status' => 'PAID',
                'fulfillment_type' => 'SHIPMENT',
                'payment_token' => 'PAYMENT_TOKEN_SAMPLE_123',
                'subtotal' => 200000.00,
                'discount' => 20000.00,
                'shipping_cost' => 15000.00,
                'total' => 195000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
