<?php

namespace App\Console\Commands;

use App\Models\GrabApiToken;
use App\Services\External\Grab\GrabAuthService;
use Illuminate\Console\Command;

class LoginGrab extends Command
{
    protected $signature = 'grab:login';
    protected $description = 'Login to Grab (OAuth2 client_credentials) and store the access token';

    public function handle(): int
    {
        try {
            $response = GrabAuthService::login();

            if (!($response['success'] ?? false)) {
                $this->error('[GRAB] Failed to login');
                return Command::FAILURE;
            }

            $data = $response['data'];

            $accessToken = $data['access_token'] ?? null;
            $expiresIn = (int) ($data['expires_in'] ?? 3600);

            if (!$accessToken) {
                $this->error('[GRAB] Access token not found in response');
                return Command::FAILURE;
            }

            GrabApiToken::saveToken(
                accessToken: $accessToken,
                expiresAt: now()->addSeconds($expiresIn)
            );

            $this->info('[GRAB] Login success');
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            report($e);
            $this->error($e->getMessage());
            return Command::FAILURE;
        }
    }
}
