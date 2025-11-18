<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonProgressResource extends JsonResource
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
            'user_id' => $this->user_id,
            'lesson_id' => $this->lesson_id,
            
            // Relationships
            'user' => new UserResource($this->whenLoaded('user')),
            'lesson' => new LessonResource($this->whenLoaded('lesson')),
            
            // Progress status
            'is_completed' => $this->is_completed,
            'status' => $this->is_completed ? 'completed' : 'in_progress',
            
            // Completion details
            'completed_at' => $this->completed_at?->toISOString(),
            'completion_date' => $this->when(
                $this->completed_at,
                fn() => $this->completed_at->format('F d, Y')
            ),
            'days_since_completion' => $this->when(
                $this->completed_at,
                fn() => $this->completed_at->diffInDays(now())
            ),
            
            // Timestamps
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'last_activity' => $this->updated_at->diffForHumans(),
        ];
    }
}