<?php

namespace App\Console\Commands;

use App\Models\OxyApiToken;
use App\Services\External\Oxy\AuthOxyService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RefreshOxyToken extends Command
{
    protected $signature = 'oxy:refresh-token';
    protected $description = 'Refresh OXY access token';

    public function handle(): int
    {
        try {
            $token = OxyApiToken::getToken();

            if (!$token || !$token->oxy_refresh_token) {
                $this->error('[OXY] Refresh token not found');
                return Command::FAILURE;
            }

            $response = AuthOxyService::refreshToken($token->oxy_refresh_token);

            if (!($response['success'] ?? false)) {
                $this->error('[OXY] Failed to refresh token');
                return Command::FAILURE;
            }

            OxyApiToken::updateAccessToken($response['data']['data']);

            $this->info('[OXY] access token refreshed');
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            report($e);
            $this->error($e->getMessage());
            return Command::FAILURE;
        }
    }
}
