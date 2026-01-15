<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OxyApiTokenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('oxy_api_tokens')->insert([
            [
                'id' => 1,
                'oxy_access_token' => 'eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwODEyMzQ1Njc4OTAiLCJmaXJzdE5hbWUiOiIiLCJleHAiOjE3NjkwOTYwNjF9.-TrvjoRrPmbENlmyo_0ukL-flnWzngmUYzUASvXZS2o',
                'oxy_refresh_token' => 'eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwODEyMzQ1Njc4OTAiLCJleHAiOjE3NjkwOTYwNjF9.venLdAb8JGHN3k1uJ7yVgri9g1v9-9S3gFY5IwZ3uLc',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
