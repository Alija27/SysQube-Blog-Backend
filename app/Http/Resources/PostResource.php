<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "slug" => $this->slug,
            "description" => $this->description,
            "status" => $this->status,
            "image" => $this->image ?
                config('app.url') . Storage::url($this->image)
                : null,
            "user_id" => $this->user_id,
            "user" => $this->user?->name,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
