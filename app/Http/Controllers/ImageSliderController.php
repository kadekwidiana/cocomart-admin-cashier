<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\ImageSlider\CreateImageSliderRequest;
use App\Http\Requests\ImageSlider\UpdateImageSliderRequest;
use App\Models\ImageSlider;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ImageSliderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $perpage   = $request->integer('perpage', 10);
            $link      = $request->input('link');
            $index     = $request->input('index');
            $is_active = $request->input('is_active');

            $query = ImageSlider::query();

            if (!empty($link)) {
                $query->where('link', 'like', "%{$link}%");
            }

            if (!empty($index)) {
                $query->where('index', $index);
            }

            if ($is_active !== null && $is_active !== '') {
                $query->where('is_active', (int) $is_active);
            }

            $imageSliders = $query
                ->latest()
                ->paginate($perpage)
                ->withQueryString();

            return Inertia::render('Backpage/ImageSlider/Index', [
                'title' => 'Image Slider',
                'imageSliders' => $imageSliders,
                'filters' => [
                    'perpage'   => $perpage,
                    'link'      => $link,
                    'index'     => $index,
                    'is_active' => $is_active,
                ],
            ]);
        } catch (\Throwable $e) {
            return back()->withErrors([
                'message' => 'Failed to fetch image sliders',
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
    public function store(CreateImageSliderRequest $request)
    {
        try {
            $validated = $request->validated();

            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $path = $request->file('image')->store('image-sliders');
                $validated['image'] = Storage::url($path);
            }

            $imageSlider = ImageSlider::create($validated);

            return ApiResponse::success($imageSlider, 'Image slider berhasil dibuat.', 201);
        } catch (Exception $e) {
            return ApiResponse::error(
                [
                    'detail' => $e->getMessage(),
                ],
                'Image slider gagal dibuat.',
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
    public function update(UpdateImageSliderRequest $request, string $id)
    {
        try {
            $validated = $request->validated();

            $imageSlider = ImageSlider::findOrFail($id);

            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                if ($imageSlider->image) {
                    Storage::delete(str_replace(Storage::url(''), '', $imageSlider->image));
                }

                $path = $request->file('image')->store('image-sliders');
                $validated['image'] = Storage::url($path);
            } else {
                $validated['image'] = $imageSlider->image;
            }

            $imageSlider->update($validated);

            return ApiResponse::success($imageSlider, 'Image slider berhasil diupdate.', 200);
        } catch (Exception $e) {
            return ApiResponse::error(
                [
                    'detail' => $e->getMessage(),
                ],
                'Image slider gagal diupdate.',
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $imageSlider = ImageSlider::findOrFail($id);

            $imageSlider->delete();

            return ApiResponse::success(null, 'Image slider berhasil dihapus.', 200);
        } catch (Exception $e) {
            return ApiResponse::error(
                [
                    'detail' => $e->getMessage(),
                ],
                'Image slider gagal dihapus.',
            );
        }
    }
}
