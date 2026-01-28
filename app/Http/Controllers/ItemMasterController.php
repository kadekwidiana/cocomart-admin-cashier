<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\ItemMaster\UploadImageItemMasterRequest;
use App\Models\ItemMasterImage;
use App\Models\OxyApiToken;
use App\Services\External\Oxy\ItemMasterOxyService;
use App\Services\External\Oxy\LocationOxyService;
use App\Utils\PaginationUtil;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ItemMasterController extends Controller
{
    public function index(Request $request)
    {
        try {
            $name = $request->name ?? null;
            $code = $request->code ?? null;
            $perpage = $request->perpage ?? 10;
            $page = $request->page ?? 1;

            $oxyAccessToken = OxyApiToken::getAccessToken();

            $response = ItemMasterOxyService::getItemMasters(
                token: $oxyAccessToken,
                name: $name,
                code: $code,
                page: $page - 1,
                size: $perpage,
            );

            if (!$response['success']) {
                $emptyPagination = PaginationUtil::emptyPagination(
                    [
                        'name' => $name,
                        'code' => $code
                    ],
                    $perpage,
                    url()->current()
                );

                return Inertia::render('Backpage/ItemMaster/Index', [
                    'title' => 'Item Master',
                    'itemMasters' => $emptyPagination,
                    'filters' => [
                        'perpage'    => $perpage,
                        'name'       => $name,
                        'code'       => $code
                    ],
                ]);
            }

            $apiData = $response['data'];

            $data = $apiData['data'];
            $pagination = $apiData['pagination'];

            $paginationFormatted = PaginationUtil::formatPaginationData(
                $data,
                $pagination,
                [
                    'name' => $name,
                    'code' => $code
                ],
                $perpage,
                url()->current()
            );

            return Inertia::render('Backpage/ItemMaster/Index', [
                'title' => 'Item Master',
                'itemMasters' => $paginationFormatted,
                'filters' => [
                    'perpage'    => $perpage,
                    'name'       => $name,
                    'code'       => $code
                ],
            ]);
        } catch (\Throwable $e) {
            return back()->withErrors([
                'message' => 'Failed to fetch itemMasters. ' . $e->getMessage(),
            ]);
        }
    }

    public function show(string $itemMasterId)
    {
        try {
            $oxyAccessToken = OxyApiToken::getAccessToken();

            $itemMasterRes = ItemMasterOxyService::getItemMasterDetail(
                token: $oxyAccessToken,
                itemMasterId: $itemMasterId,
                locationId: null,
            );

            $locationsRes = LocationOxyService::getLocations(
                token: $oxyAccessToken,
            );

            if (!$itemMasterRes['success']) {
                return back()->withErrors([
                    'message' => 'Failed to fetch itemMaster. ' . $itemMasterRes['message'],
                ]);
            }

            if (!$locationsRes['success']) {
                return back()->withErrors([
                    'message' => 'Failed to fetch locations. ' . $locationsRes['message'],
                ]);
            }

            $itemMaster = $itemMasterRes['data']['data'][0];
            $locations = $locationsRes['data']['data'];

            $images = ItemMasterImage::where('oxy_item_master_id', $itemMaster['itemMasterId'])->get();

            return Inertia::render('Backpage/ItemMaster/Detail', [
                'title' => 'Item Master',
                'itemMaster' => $itemMaster,
                'images' => $images,
                'locations' => $locations
            ]);
        } catch (\Throwable $e) {
            return back()->withErrors([
                'message' => 'Failed to fetch itemMaster. ' . $e->getMessage(),
            ]);
        }
    }

    public function addImage(UploadImageItemMasterRequest $request, string $oxyItemMasterId)
    {
        try {
            $validated = $request->validated();

            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $path = $request->file('image')->store('item-masters');
                $validated['image'] = Storage::url($path);
            }

            $itemMasterImage = ItemMasterImage::create([
                'oxy_item_master_id' => $oxyItemMasterId,
                'image' => $validated['image']
            ]);

            return ApiResponse::success($itemMasterImage, 'Item master image successfully created.', 201);
        } catch (Exception $e) {
            return ApiResponse::error(
                [
                    'detail' => $e->getMessage(),
                ],
                'Item master image failed to create.',
            );
        }
    }

    public function deleteImage(string $id)
    {
        try {
            $itemMasterImage = ItemMasterImage::findOrFail($id);

            $itemMasterImage->delete();

            return ApiResponse::success(null, 'Item master image successfully deleted.', 200);
        } catch (Exception $e) {
            return ApiResponse::error(
                [
                    'detail' => $e->getMessage(),
                ],
                'Item master image failed to delete.',
            );
        }
    }
}
