<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SectionResource extends JsonResource
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
            'order' => $this->order,
            
            // Relationships
            'course_id' => $this->course_id,
            'course' => new CourseResource($this->whenLoaded('course')),
            'lessons' => LessonResource::collection($this->whenLoaded('lessons')),
            
            // Computed attributes
            'lessons_count' => $this->when(
                $this->relationLoaded('lessons'),
                fn() => $this->lessonsCount()
            ),
            'total_duration' => $this->when(
                $this->relationLoaded('lessons'),
                fn() => [
                    'minutes' => $this->totalDuration(),
                    'formatted' => $this->formatDuration($this->totalDuration()),
                ]
            ),
            
            // Timestamps
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }

    /**
     * Format duration in a human-readable format.
     */
    protected function formatDuration(int $minutes): string
    {
        if ($minutes < 60) {
            return "{$minutes}m";
        }
        
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;
        
        return $remainingMinutes > 0 
            ? "{$hours}h {$remainingMinutes}m" 
            : "{$hours}h";
    }
}