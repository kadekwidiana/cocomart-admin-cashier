<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\Location\UploadImageLocationRequest;
use App\Models\LocationImage;
use App\Models\OxyApiToken;
use App\Services\External\Oxy\LocationOxyService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        try {
            $name = $request->name ?? null;
            $code = $request->code ?? null;

            $oxyAccessToken = OxyApiToken::getAccessToken();

            $response = LocationOxyService::getLocations(
                token: $oxyAccessToken,
                name: $name,
                code: $code
            );

            if (!$response['success']) {
                return Inertia::render('Backpage/Location/Index', [
                    'title' => 'Location',
                    'locations' => [],
                    'filters' => [
                        'name'       => $name,
                        'code'       => $code
                    ],
                ]);
            }

            $apiData = $response['data'];

            $data = $apiData['data'];

            return Inertia::render('Backpage/Location/Index', [
                'title' => 'Location',
                'locations' => $data,
                'filters' => [
                    'name'       => $name,
                    'code'       => $code
                ],
            ]);
        } catch (\Throwable $e) {
            return back()->withErrors([
                'message' => 'Failed to fetch locations. ' . $e->getMessage(),
            ]);
        }
    }

    public function show(string $code)
    {
        try {
            $oxyAccessToken = OxyApiToken::getAccessToken();

            $response = LocationOxyService::getLocations(
                token: $oxyAccessToken,
                code: $code,
            );

            if (!$response['success']) {
                return back()->withErrors([
                    'message' => 'Failed to fetch location. ' . $response['message'],
                ]);
            }

            $location = $response['data']['data'][0];

            $images = LocationImage::where('oxy_location_id', $location['id'])->get();

            return Inertia::render('Backpage/Location/Detail', [
                'title' => 'Location',
                'location' => $location,
                'images' => $images
            ]);
        } catch (\Throwable $e) {
            return back()->withErrors([
                'message' => 'Failed to fetch location. ' . $e->getMessage(),
            ]);
        }
    }

    public function addImage(UploadImageLocationRequest $request, string $oxyLocationId)
    {
        try {
            $validated = $request->validated();

            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $path = $request->file('image')->store('locations');
                $validated['image'] = Storage::url($path);
            }

            $locationImage = LocationImage::create([
                'oxy_location_id' => $oxyLocationId,
                'image' => $validated['image']
            ]);

            return ApiResponse::success($locationImage, 'Location image successfully created.', 201);
        } catch (Exception $e) {
            return ApiResponse::error(
                [
                    'detail' => $e->getMessage(),
                ],
                'Location image failed to create.',
            );
        }
    }

    public function deleteImage(string $id)
    {
        try {
            $locationImage = LocationImage::findOrFail($id);

            $locationImage->delete();

            return ApiResponse::success(null, 'Location image successfully deleted.', 200);
        } catch (Exception $e) {
            return ApiResponse::error(
                [
                    'detail' => $e->getMessage(),
                ],
                'Location image failed to delete.',
            );
        }
    }
}
