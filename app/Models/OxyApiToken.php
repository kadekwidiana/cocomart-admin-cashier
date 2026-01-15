<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OxyApiToken extends Model
{
    use HasFactory;

    protected $table = 'oxy_api_tokens';

    protected $fillable = [
        'oxy_access_token',
        'oxy_refresh_token',
    ];

    public static function getToken(): ?self
    {
        return self::query()->first();
    }

    public static function getAccessToken(): ?string
    {
        return self::query()->value('oxy_access_token');
    }

    public static function saveToken(string $accessToken, ?string $refreshToken = null): self
    {
        return self::updateOrCreate(
            ['id' => 1],
            [
                'oxy_access_token'  => $accessToken,
                'oxy_refresh_token' => $refreshToken,
            ]
        );
    }

    public static function updateAccessToken(string $accessToken): self
    {
        return self::updateOrCreate(
            ['id' => 1],
            [
                'oxy_access_token' => $accessToken,
            ]
        );
    }
}
