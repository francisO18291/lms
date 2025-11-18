<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
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
            'content' => $this->when(
                $this->canAccess($request->user()),
                $this->content
            ),
            'type' => $this->type,
            'video_url' => $this->when(
                $this->canAccess($request->user()) && $this->type === 'video',
                $this->video_url
            ),
            'duration' => [
                'minutes' => $this->duration_minutes,
                'formatted' => $this->formattedDuration(),
            ],
            'order' => $this->order,
            'is_preview' => $this->is_preview,
            'can_preview' => $this->canPreview(),
            
            // Relationships
            'section_id' => $this->section_id,
            'section' => new SectionResource($this->whenLoaded('section')),
            
            // User progress (if authenticated)
            'progress' => $this->when(
                $request->user() && $this->relationLoaded('progress'),
                function () use ($request) {
                    $userProgress = $this->progress
                        ->where('user_id', $request->user()->id)
                        ->first();
                    
                    return $userProgress ? new LessonProgressResource($userProgress) : null;
                }
            ),
            'is_completed' => $this->when(
                $request->user(),
                fn() => $request->user()->hasCompletedLesson($this)
            ),
            
            // Timestamps
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            
            // URLs
            'url' => $this->when(
                $this->relationLoaded('section'),
                fn() => route('courses.lessons.show', [
                    $this->section->course,
                    $this->slug
                ])
            ),
        ];
    }

    /**
     * Check if user can access lesson content.
     */
    protected function canAccess($user): bool
    {
        if (!$user) {
            return $this->is_preview;
        }

        if ($user->isAdmin() || $user->isTeacher()) {
            return true;
        }

        if ($this->is_preview) {
            return true;
        }

        // Check if user is enrolled in the course
        if ($this->relationLoaded('section')) {
            return $user->isEnrolledIn($this->section->course);
        }

        return false;
    }
}