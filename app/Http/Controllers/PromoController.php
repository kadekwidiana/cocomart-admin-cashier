<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\Promo\CreatePromoRequest;
use App\Http\Requests\Promo\UpdatePromoRequest;
use App\Models\Promo;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PromoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $perpage    = $request->integer('perpage', 10);
            $title     = $request->input('title');
            $code     = $request->input('code');
            $is_active  = $request->input('is_active');
            $start_date = $request->input('start_date');
            $end_date   = $request->input('end_date');

            $query = Promo::query();

            if (!empty($title)) {
                $query->where('title', 'like', "%{$title}%");
            }

            if (!empty($code)) {
                $query->where('code', 'like', "%{$code}%");
            }

            if ($is_active !== null && $is_active !== '') {
                $query->where('is_active', (bool) $is_active);
            }

            if (!empty($start_date)) {
                $query->whereDate('start_date', '>=', $start_date);
            }

            if (!empty($end_date)) {
                $query->whereDate('end_date', '<=', $end_date);
            }

            $promos = $query
                ->latest()
                ->paginate($perpage)
                ->withQueryString();

            return Inertia::render('Backpage/Promo/Index', [
                'title' => 'Promo',
                'promos' => $promos,
                'filters' => [
                    'perpage'    => $perpage,
                    'title'      => $title,
                    'code'       => $code,
                    'is_active'  => $is_active,
                    'start_date' => $start_date,
                    'end_date'   => $end_date,
                ],
            ]);
        } catch (\Throwable $e) {
            return back()->withErrors([
                'message' => 'Failed to fetch promos',
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
    public function store(CreatePromoRequest $request)
    {
        try {
            $validated = $request->validated();

            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $path = $request->file('image')->store('promos');
                $validated['image'] = Storage::url($path);
            }

            $promo = Promo::create($validated);

            return ApiResponse::success($promo, 'Promo successfully created.', 201);
        } catch (Exception $e) {
            return ApiResponse::error(
                [
                    'detail' => $e->getMessage(),
                ],
                'Promo failed to create.',
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
    public function update(UpdatePromoRequest $request, string $id)
    {
        try {
            $validated = $request->validated();

            $promo = Promo::findOrFail($id);

            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                if ($promo->image) {
                    Storage::delete(str_replace(Storage::url(''), '', $promo->image));
                }

                $path = $request->file('image')->store('promos');
                $validated['image'] = Storage::url($path);
            } else {
                $validated['image'] = $promo->image;
            }

            $promo->update($validated);

            return ApiResponse::success($promo, 'Promo successfully updated.', 200);
        } catch (Exception $e) {
            return ApiResponse::error(
                [
                    'detail' => $e->getMessage(),
                ],
                'Promo failed to update.',
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $promo = Promo::findOrFail($id);

            $promo->delete();

            return ApiResponse::success(null, 'Promo successfully deleted.', 200);
        } catch (Exception $e) {
            return ApiResponse::error(
                [
                    'detail' => $e->getMessage(),
                ],
                'Promo failed to delete.',
            );
        }
    }
}
