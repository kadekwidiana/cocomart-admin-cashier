<?php

namespace App\Services\External\Grab;

use App\Helpers\ErrorHandler;
use Exception;
use Illuminate\Support\Facades\Http;

class GrabAuthService
{
    public static function login()
    {
        $endpoint = rtrim((string) config('services.grab.endpoint'), '/');

        $url = $endpoint . '/grabid/v1/oauth2/token';

        try {
            $response = Http::asForm()->post($url, [
                'grant_type'    => 'client_credentials',
                'client_id'     => config('services.grab.client_id'),
                'client_secret' => config('services.grab.client_secret'),
                'scope'         => config('services.grab.scope'),
            ]);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'code'    => $response->status(),
                    'message' => $response->json('message') ?? 'Grab login failed.',
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
