<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;

class PaginationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if (!$this->resource instanceof LengthAwarePaginator) {
            return [];
        }

        return [
            'total' => $this->resource->total(),
            'current_page' => $this->resource->currentPage(),
            'last_page' => $this->resource->lastPage(),
            'per_page' => $this->resource->perPage(),
            'from' => $this->resource->firstItem(),
            'to' => $this->resource->lastItem(),
            'sort' => $this->additional['sort'] ?? 'asc',
            'sort_by' => $this->additional['sort_by'] ?? 'created_at',
            // 'next_page_url' => $this->resource->nextPageUrl(),
            // 'prev_page_url' => $this->resource->previousPageUrl(),
            // 'first_page_url' => $this->resource->url(1),
            // 'last_page_url' => $this->resource->url($this->resource->lastPage()),
            // 'links' => $this->generatePaginationLinks(),
        ];
    }

    /**
     * Generate pagination links.
     *
     * @return array
     */
    private function generatePaginationLinks(): array
    {
        $links = [];

        // Previous link
        $links[] = [
            'url' => $this->resource->previousPageUrl(),
            'label' => '&laquo; Previous',
            'active' => $this->resource->currentPage() > 1,
        ];

        // Loop untuk semua halaman
        for ($page = 1; $page <= $this->resource->lastPage(); $page++) {
            $links[] = [
                'url' => $this->resource->url($page),
                'label' => (string) $page,
                'active' => $this->resource->currentPage() == $page,
            ];
        }

        // Next link
        $links[] = [
            'url' => $this->resource->nextPageUrl(),
            'label' => 'Next &raquo;',
            'active' => $this->resource->hasMorePages(),
        ];

        return $links;
    }
}
