<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\Category\UploadImageCategoryRequest;
use App\Models\CategoryImage;
use App\Models\OxyApiToken;
use App\Services\External\Oxy\CategoryOxyService;
use App\Utils\PaginationUtil;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        try {
            $name = $request->name ?? null;
            $code = $request->code ?? null;
            $perpage = $request->perpage ?? 10;
            $page = $request->page ?? 1;

            $oxyAccessToken = OxyApiToken::getAccessToken();

            $response = CategoryOxyService::getCategories(
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

                return Inertia::render('Backpage/Category/Index', [
                    'title' => 'Category',
                    'categories' => $emptyPagination,
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

            return Inertia::render('Backpage/Category/Index', [
                'title' => 'Category',
                'categories' => $paginationFormatted,
                'filters' => [
                    'perpage'    => $perpage,
                    'name'       => $name,
                    'code'       => $code
                ],
            ]);
        } catch (\Throwable $e) {
            return back()->withErrors([
                'message' => 'Failed to fetch categories. ' . $e->getMessage(),
            ]);
        }
    }

    public function show(string $code)
    {
        try {
            $oxyAccessToken = OxyApiToken::getAccessToken();

            $response = CategoryOxyService::getCategories(
                token: $oxyAccessToken,
                code: $code,
            );

            if (!$response['success']) {
                return back()->withErrors([
                    'message' => 'Failed to fetch category. ' . $response['message'],
                ]);
            }

            $category = $response['data']['data'][0];

            $images = CategoryImage::where('oxy_category_id', $category['categoryId'])->get();

            return Inertia::render('Backpage/Category/Detail', [
                'title' => 'Category',
                'category' => $category,
                'images' => $images
            ]);
        } catch (\Throwable $e) {
            return back()->withErrors([
                'message' => 'Failed to fetch category. ' . $e->getMessage(),
            ]);
        }
    }

    public function addImage(UploadImageCategoryRequest $request, string $oxyCategoryId)
    {
        try {
            $validated = $request->validated();

            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $path = $request->file('image')->store('categories');
                $validated['image'] = Storage::url($path);
            }

            $categoryImage = CategoryImage::create([
                'oxy_category_id' => $oxyCategoryId,
                'image' => $validated['image']
            ]);

            return ApiResponse::success($categoryImage, 'Category image successfully created.', 201);
        } catch (Exception $e) {
            return ApiResponse::error(
                [
                    'detail' => $e->getMessage(),
                ],
                'Category image failed to create.',
            );
        }
    }

    public function deleteImage(string $id)
    {
        try {
            $categoryImage = CategoryImage::findOrFail($id);

            $categoryImage->delete();

            return ApiResponse::success(null, 'Category image successfully deleted.', 200);
        } catch (Exception $e) {
            return ApiResponse::error(
                [
                    'detail' => $e->getMessage(),
                ],
                'Category image failed to delete.',
            );
        }
    }
}
