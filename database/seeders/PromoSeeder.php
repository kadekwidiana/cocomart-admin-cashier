<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PromoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('promos')->insert([
            [
                'id' => 1,
                'title' => 'Test Promo',
                'code' => 'PROMOV01',
                'discount_percentage' => 20,
                'is_active' => true,
                'image' => '/assets/icons/cocomart-logo-text.png',
                'start_date' => now()->startOfMonth(),
                'end_date' => now()->endOfMonth(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
