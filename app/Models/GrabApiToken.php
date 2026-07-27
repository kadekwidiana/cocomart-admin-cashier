<?php

namespace App\Models;

use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class GrabApiToken extends Model
{
    use HasFactory;

    protected $table = 'grab_api_tokens';

    protected $fillable = [
        'grab_access_token',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public static function getToken(): ?self
    {
        return self::query()->first();
    }

    public static function getAccessToken(): ?string
    {
        return self::query()->value('grab_access_token');
    }

    public static function saveToken(string $accessToken, ?\DateTimeInterface $expiresAt = null): self
    {
        return self::updateOrCreate(
            ['id' => 1],
            [
                'grab_access_token' => $accessToken,
                'expires_at'        => $expiresAt,
            ]
        );
    }

    protected static function isExpiring(?self $token): bool
    {
        return !$token
            || !$token->grab_access_token
            || !$token->expires_at
            || $token->expires_at->subMinutes(2)->isPast();
    }


    public static function getValidAccessToken(): ?string
    {
        $token = self::getToken();

        if (!self::isExpiring($token)) {
            return $token->grab_access_token;
        }

        $lock = Cache::lock('grab:login-lock', 15);
        $acquired = false;

        try {
            $acquired = $lock->block(15);

            $token = self::getToken();
            if (self::isExpiring($token)) {
                Artisan::call('grab:login');
                $token = self::getToken();
            }
        } catch (LockTimeoutException $e) {
            $token = self::getToken();
        } finally {
            if ($acquired) {
                $lock->release();
            }
        }

        return $token?->grab_access_token;
    }
}
