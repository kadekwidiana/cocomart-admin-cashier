<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class OxyAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $authorization = $request->header('Authorization');

        if (!$authorization || !str_starts_with($authorization, 'Bearer ')) {
            return ApiResponse::error(
                data: null,
                message: 'Authorization bearer token is required',
                statusCode: Response::HTTP_UNAUTHORIZED
            );
        }

        $baseUrl = env('OXY_BASE_URL_API');

        $oxyCustomerId = env('OXY_CUSTOMER_ID');

        $url = $baseUrl . '/membership/api/loyalty/point/total/' . $oxyCustomerId;

        try {
            $response = Http::withHeaders([
                'Authorization' => $authorization
            ])->get($url);

            if ($response->failed()) {
                return ApiResponse::error(
                    data: null,
                    message: $response->json('message') ?? 'OXY authentication service unavailable',
                    statusCode: $response->status()
                );
            }
        } catch (\Throwable $e) {
            return ApiResponse::error(
                data: null,
                message: $e->getMessage(),
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $next($request);
    }
}
