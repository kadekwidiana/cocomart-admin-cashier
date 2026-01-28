<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\Users\CreateUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Models\OxyApiToken;
use App\Models\User;
use App\Services\External\Oxy\LocationOxyService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $perpage    = $request->integer('perpage', 10);
            $name     = $request->input('name');
            $role     = $request->input('role');
            $is_active  = $request->input('is_active');

            $query = User::query();

            if (!empty($name)) {
                $query->where('name', 'like', "%{$name}%");
            }

            if (!empty($role)) {
                $query->where('role', $role);
            }

            if ($is_active !== null && $is_active !== '') {
                $query->where('is_active', (bool) $is_active);
            }

            $users = $query
                ->latest()
                ->paginate($perpage)
                ->withQueryString();

            $oxyAccessToken = OxyApiToken::getAccessToken();

            $locationsRes = LocationOxyService::getLocations(
                token: $oxyAccessToken,
            );

            if (!$locationsRes['success']) {
                return back()->withErrors([
                    'message' => 'Failed to fetch locations. ' . $locationsRes['message'],
                ]);
            }

            $locations = $locationsRes['data']['data'];

            return Inertia::render('Backpage/User/Index', [
                'title' => 'User',
                'users' => $users,
                'filters' => [
                    'perpage'    => $perpage,
                    'name'      => $name,
                    'role'      => $role,
                    'is_active'  => $is_active
                ],
                'locations' => $locations
            ]);
        } catch (\Throwable $e) {
            return back()->withErrors([
                'message' => 'Failed to fetch users',
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
    public function store(CreateUserRequest $request)
    {
        try {
            $validated = $request->validated();

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
                'phone_number' => $validated['phone_number'],
                'address' => $validated['address'],
                'oxy_location_id' => $validated['oxy_location_id'] ?? null,
            ]);

            return ApiResponse::success($user, 'User successfully created.', 201);
        } catch (Exception $e) {
            return ApiResponse::error(
                [
                    'detail' => $e->getMessage(),
                ],
                'User failed to create.',
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
    public function update(UpdateUserRequest $request, string $id)
    {
        try {
            $validated = $request->validated();

            $user = User::findOrFail($id);

            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
                'phone_number' => $validated['phone_number'],
                'address' => $validated['address'],
                'oxy_location_id' => $validated['oxy_location_id'] ?? null,
            ]);

            return ApiResponse::success($user, 'User successfully updated.', 200);
        } catch (Exception $e) {
            return ApiResponse::error(
                [
                    'detail' => $e->getMessage(),
                ],
                'User failed to update.',
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = User::findOrFail($id);

            $user->delete();

            return ApiResponse::success(null, 'User successfully deleted.', 200);
        } catch (Exception $e) {
            return ApiResponse::error(
                [
                    'detail' => $e->getMessage(),
                ],
                'User failed to delete.',
            );
        }
    }
}
