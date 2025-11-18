<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
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
            'slug' => $this->slug,
            'description' => $this->description,
            'requirements' => $this->requirements,
            'learning_outcomes' => $this->learning_outcomes,
            'thumbnail' => $this->thumbnail ? asset('storage/' . $this->thumbnail) : null,
            'price' => [
                'amount' => (float) $this->price,
                'formatted' => '$' . number_format($this->price, 2),
                'is_free' => $this->isFree(),
            ],
            'level' => $this->level,
            'status' => $this->status,
            'duration_hours' => $this->duration_hours,
            'is_featured' => $this->is_featured,
            'is_published' => $this->isPublished(),
            
            // Relationships
            'teacher' => new UserResource($this->whenLoaded('teacher')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'sections' => SectionResource::collection($this->whenLoaded('sections')),
            
            // Computed attributes
            'total_lessons' => $this->when(
                $this->relationLoaded('lessons'),
                fn() => $this->totalLessons()
            ),
            'students_count' => $this->when(
                $this->relationLoaded('students') || isset($this->enrollments_count),
                fn() => $this->studentsCount()
            ),
            
            // Timestamps
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            
            // URLs
            'url' => route('courses.show', $this->slug),
            'api_url' => route('api.courses.show', $this->id),
        ];
    
    }
}
