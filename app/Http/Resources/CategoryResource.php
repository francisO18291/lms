<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'icon' => $this->icon,
            
            // Relationships
            'courses' => CourseResource::collection($this->whenLoaded('courses')),
            
            // Computed attributes
            'courses_count' => $this->when(
                isset($this->courses_count) || $this->relationLoaded('courses'),
                fn() => $this->courses_count ?? $this->courses->count()
            ),
            
            // Timestamps
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            
            // URLs
            'url' => route('categories.show', $this->slug),
        ];
    }
}
