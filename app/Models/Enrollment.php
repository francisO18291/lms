<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'course_id',
        'price_paid',
        'progress',
        'completed_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price_paid' => 'decimal:2',
            'progress' => 'integer',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns this enrollment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course for this enrollment.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Check if the course is completed.
     */
    public function isCompleted(): bool
    {
        return $this->progress === 100 && $this->completed_at !== null;
    }

    /**
     * Check if the course is in progress.
     */
    public function isInProgress(): bool
    {
        return $this->progress > 0 && $this->progress < 100;
    }

    /**
     * Check if the course is not started.
     */
    public function isNotStarted(): bool
    {
        return $this->progress === 0;
    }

    /**
     * Mark the course as completed.
     */
    public function markAsCompleted(): bool
    {
        $this->progress = 100;
        $this->completed_at = now();
        return $this->save();
    }

    /**
     * Update progress based on completed lessons.
     */
    public function updateProgress(): bool
    {
        $totalLessons = $this->course->totalLessons();
        
        if ($totalLessons === 0) {
            return false;
        }

        $completedLessons = LessonProgress::where('user_id', $this->user_id)
            ->whereHas('lesson.section', function ($query) {
                $query->where('course_id', $this->course_id);
            })
            ->where('is_completed', true)
            ->count();

        $this->progress = round(($completedLessons / $totalLessons) * 100);

        if ($this->progress === 100) {
            $this->completed_at = now();
        }

        return $this->save();
    }

    /**
     * Get progress percentage as formatted string.
     */
    public function progressPercentage(): string
    {
        return $this->progress . '%';
    }

    /**
     * Scope a query to only include completed enrollments.
     */
    public function scopeCompleted($query)
    {
        return $query->where('progress', 100)->whereNotNull('completed_at');
    }

    /**
     * Scope a query to only include in-progress enrollments.
     */
    public function scopeInProgress($query)
    {
        return $query->where('progress', '>', 0)->where('progress', '<', 100);
    }

    /**
     * Scope a query to only include not started enrollments.
     */
    public function scopeNotStarted($query)
    {
        return $query->where('progress', 0);
    }
}