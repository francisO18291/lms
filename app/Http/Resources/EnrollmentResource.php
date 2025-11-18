<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnrollmentResource extends JsonResource
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
            'course_id' => $this->course_id,
            
            // Relationships
            'user' => new UserResource($this->whenLoaded('user')),
            'course' => new CourseResource($this->whenLoaded('course')),
            
            // Payment information
            'price_paid' => [
                'amount' => (float) $this->price_paid,
                'formatted' => '$' . number_format($this->price_paid, 2),
            ],
            
            // Progress information
            'progress' => [
                'percentage' => $this->progress,
                'formatted' => $this->progressPercentage(),
                'status' => $this->getProgressStatus(),
            ],
            'is_completed' => $this->isCompleted(),
            'is_in_progress' => $this->isInProgress(),
            'is_not_started' => $this->isNotStarted(),
            
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
            
            // Statistics
            'days_enrolled' => $this->created_at->diffInDays(now()),
            'last_activity' => [
                'date' => $this->updated_at->toISOString(),
                'days_ago' => $this->updated_at->diffInDays(now()),
                'human_readable' => $this->updated_at->diffForHumans(),
            ],
            
            // Timestamps
            'enrolled_at' => $this->created_at->toISOString(),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            
            // URLs
            'certificate_url' => $this->when(
                $this->isCompleted(),
                fn() => route('enrollments.certificate', $this->id)
            ),
            'course_url' => $this->when(
                $this->relationLoaded('course'),
                fn() => route('courses.show', $this->course->slug)
            ),
        ];
    }

    /**
     * Get progress status text.
     */
    protected function getProgressStatus(): string
    {
        if ($this->isCompleted()) {
            return 'completed';
        }
        
        if ($this->isInProgress()) {
            return 'in_progress';
        }
        
        return 'not_started';
    }
}