<?php

namespace App\Http\Controllers\API;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\Notification\ReadNotificationRequest;
use App\Http\Resources\NotificationReadResponseResource;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\PaginationResource;
use App\Models\Notification;
use App\Models\NotificationRead;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NotificationController extends Controller
{
    public function index(Request $request, string $oxyCustomerId)
    {
        try {
            $page = $request->page ?? 1;
            $size = $request->size ?? 10;
            $title = $request->title ?? null;

            if (!$oxyCustomerId) {
                return ApiResponse::error(
                    data: null,
                    message: 'Oxy customer id is required',
                    statusCode: Response::HTTP_UNAUTHORIZED
                );
            }

            $notifications = Notification::query()
                ->with([
                    'notificationReadOne' => function ($q) use ($oxyCustomerId) {
                        $q->where('oxy_customer_id', $oxyCustomerId);
                    }
                ])
                ->when(
                    $title,
                    fn($q) => $q->where('title', 'like', '%' . $title . '%')
                )
                ->paginate(
                    $size,
                    ['*'],
                    'page',
                    $page
                )->appends([
                    'page' => $page,
                    'size' => $size
                ]);

            return ApiResponse::success([
                'data' => NotificationResource::collection($notifications),
                'pagination' => new PaginationResource($notifications),
            ], 'Notifications retrieved successfully');
        } catch (\Throwable $e) {
            return ApiResponse::error([
                'detail' => $e->getMessage(),
            ]);
        }
    }

    public function count()
    {
        return ApiResponse::success(Notification::count(), 'Notifications count retrieved successfully');
    }

    public function read(ReadNotificationRequest $request)
    {
        try {
            $validated = $request->validated();

            $notification = Notification::findOrFail($validated['notificationId']);

            if (empty($notification)) {
                return ApiResponse::error(
                    [
                        'detail' => 'Notification not found',
                    ],
                    'Notification not found',
                    404
                );
            }

            $read = NotificationRead::firstOrCreate(
                [
                    'oxy_customer_id' => $validated['oxyCustomerId'],
                    'notification_id' => $notification->id
                ]
            );

            if (!$read->wasRecentlyCreated) {
                return ApiResponse::success(new NotificationReadResponseResource($read), 'Notification already read');
            }

            return ApiResponse::success(new NotificationReadResponseResource($read), 'Notification read successfully');
        } catch (\Exception $e) {
            return ApiResponse::error(
                [
                    'detail' => $e->getMessage(),
                ]
            );
        }
    }
}
