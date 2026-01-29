<?php

namespace App\Console\Commands;

use App\Models\OxyApiToken;
use App\Services\External\Oxy\AuthOxyService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class LoginOxy extends Command
{
    protected $signature = 'oxy:login';
    protected $description = 'Login to Oxy';

    public function handle(): int
    {
        try {
            $username = env('OXY_USERNAME_LOGIN');
            $password = env('OXY_PASSWORD_LOGIN');

            $response = AuthOxyService::login(
                username: $username,
                password: $password
            );

            if (!($response['success'] ?? false)) {
                $this->error('[OXY] Failed to login');
                return Command::FAILURE;
            }

            OxyApiToken::saveToken(
                accessToken: $response['data']['data']['token'],
                refreshToken: $response['data']['data']['refreshToken']
            );

            $this->info('[OXY] Login success');
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            report($e);
            $this->error($e->getMessage());
            return Command::FAILURE;
        }
    }
}
