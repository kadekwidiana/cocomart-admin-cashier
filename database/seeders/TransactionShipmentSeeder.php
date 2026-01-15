<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionShipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('transaction_shipments')->insert([
            [
                'id' => 1,
                'transaction_id' => 2,
                'grab_delivery_id' => 'GRAB-DELIVERY-0001',
                'grab_shipping_cost' => 25000,
                'status' => 'PENDING',
                'grab_json_response' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
