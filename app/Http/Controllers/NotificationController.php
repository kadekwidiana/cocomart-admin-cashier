<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\Notification\CreateNotificationRequest;
use App\Http\Requests\Notification\UpdateNotificationRequest;
use App\Models\Notification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $perpage    = $request->integer('perpage', 10);
            $title     = $request->input('title');
            $type     = $request->input('type');

            $query = Notification::query();

            if (!empty($title)) {
                $query->where('title', 'like', "%{$title}%");
            }

            if (!empty($type)) {
                $query->where('type', $type);
            }

            $notifications = $query
                ->latest()
                ->paginate($perpage)
                ->withQueryString();

            return Inertia::render('Backpage/Notification/Index', [
                'title' => 'Promo',
                'notifications' => $notifications,
                'filters' => [
                    'perpage'    => $perpage,
                    'title'      => $title,
                    'type'       => $type
                ],
            ]);
        } catch (\Throwable $e) {
            return back()->withErrors([
                'message' => 'Failed to fetch notifications. ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateNotificationRequest $request)
    {
        try {
            $validated = $request->validated();

            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $path = $request->file('image')->store('notifications');
                $validated['image'] = Storage::url($path);
            }

            $notification = Notification::create($validated);

            return ApiResponse::success($notification, 'Notification successfully created.', 201);
        } catch (Exception $e) {
            return ApiResponse::error(
                [
                    'detail' => $e->getMessage(),
                ],
                'Notification failed to create.',
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNotificationRequest $request, string $id)
    {
        try {
            $validated = $request->validated();

            $notification = Notification::findOrFail($id);

            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                if ($notification->image) {
                    Storage::delete(str_replace(Storage::url(''), '', $notification->image));
                }

                $path = $request->file('image')->store('notifications');
                $validated['image'] = Storage::url($path);
            } else {
                $validated['image'] = $notification->image;
            }

            $notification->update($validated);

            return ApiResponse::success($notification, 'Notification successfully updated.', 200);
        } catch (Exception $e) {
            return ApiResponse::error(
                [
                    'detail' => $e->getMessage(),
                ],
                'Notification failed to update.',
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $notification = Notification::findOrFail($id);

            $notification->delete();

            return ApiResponse::success(null, 'Notification successfully deleted.', 200);
        } catch (Exception $e) {
            return ApiResponse::error(
                [
                    'detail' => $e->getMessage(),
                ],
                'Notification failed to delete.',
            );
        }
    }
}
