<?php

namespace App\Services\External\Oxy;

use App\Helpers\ErrorHandler;
use Exception;
use Illuminate\Support\Facades\Http;

class AuthOxyService
{
    public static function login(
        $username,
        $password,
    ) {
        $baseUrl = env('OXY_BASE_URL_API');

        $url = $baseUrl . '/membership/api/auth/customer';

        try {
            $response = Http::asForm()
                ->post($url, [
                    'username' => $username,
                    'password' => $password,
                ]);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'code'    => $response->status(),
                    'message' => $response->json('message') ?? 'Login failed.',
                    'error'   => $response->json(),
                ];
            }

            return [
                'success' => true,
                'code'    => $response->status(),
                'data'    => $response->json(),
            ];
        } catch (Exception $e) {
            return ErrorHandler::apiErrorResponse($e->getMessage());
        }
    }

    public static function refreshToken(
        $refreshToken,
    ) {
        $baseUrl = env('OXY_BASE_URL_API');

        $url = $baseUrl . '/membership/api/auth/customer/refresh/' . $refreshToken;

        try {
            $response = Http::post($url);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'code'    => $response->status(),
                    'message' => $response->json('message') ?? 'Login failed.',
                    'error'   => $response->json(),
                ];
            }

            return [
                'success' => true,
                'code'    => $response->status(),
                'data'    => $response->json(),
            ];
        } catch (Exception $e) {
            return ErrorHandler::apiErrorResponse($e->getMessage());
        }
    }
}
