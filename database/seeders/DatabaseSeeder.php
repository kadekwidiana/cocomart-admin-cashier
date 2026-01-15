<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategoryImageSeeder::class,
            ItemMasterImageSeeder::class,
            StoreImageSeeder::class,
            ImageSliderSeeder::class,
            PromoSeeder::class,
            TransactionSeeder::class,
            TransactionItemSeeder::class,
            TransactionPickupSeeder::class,
            TransactionShipmentSeeder::class,
            NotificationSeeder::class,
            OxyApiTokenSeeder::class
        ]);
    }
}
