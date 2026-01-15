<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('notifications')->insert([
            [
                'id' => 1,
                'title' => 'Informasi Sistem',
                'image' => '/assets/images/logo.png',
                'body' => 'Saat ini sistem berjalan normal. Terima kasih telah menggunakan layanan kami.',
                'type' => 'modal',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'title' => 'Promo Global',
                'image' => '/assets/images/logo.png',
                'body' => 'Nikmati promo menarik yang berlaku untuk semua pengguna hari ini.',
                'type' => 'modal',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
