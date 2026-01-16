<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'image' => $this->image ? url($this->image) : null,
            'type' => $this->type,
            'body' => $this->body,
            'isRead' => (bool) $this->notificationReadOne !== false,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
