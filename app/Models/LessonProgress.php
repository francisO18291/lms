<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonProgress extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'lesson_progress';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'lesson_id',
        'is_completed',
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
            'is_completed' => 'boolean',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns this progress record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the lesson for this progress record.
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * Mark lesson as completed.
     */
    public function markAsCompleted(): bool
    {
        $this->is_completed = true;
        $this->completed_at = now();
        $saved = $this->save();

        // Update enrollment progress
        if ($saved) {
            $this->updateEnrollmentProgress();
        }

        return $saved;
    }

    /**
     * Mark lesson as incomplete.
     */
    public function markAsIncomplete(): bool
    {
        $this->is_completed = false;
        $this->completed_at = null;
        $saved = $this->save();

        // Update enrollment progress
        if ($saved) {
            $this->updateEnrollmentProgress();
        }

        return $saved;
    }

    /**
     * Update the related enrollment progress.
     */
    protected function updateEnrollmentProgress(): void
    {
        $courseId = $this->lesson->section->course_id;
        
        $enrollment = Enrollment::where('user_id', $this->user_id)
            ->where('course_id', $courseId)
            ->first();

        if ($enrollment) {
            $enrollment->updateProgress();
        }
    }

    /**
     * Scope a query to only include completed lessons.
     */
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    /**
     * Scope a query to only include incomplete lessons.
     */
    public function scopeIncomplete($query)
    {
        return $query->where('is_completed', false);
    }

    /**
     * Get or create progress record for a user and lesson.
     */
    public static function getOrCreateProgress(int $userId, int $lessonId): self
    {
        return static::firstOrCreate(
            [
                'user_id' => $userId,
                'lesson_id' => $lessonId,
            ],
            [
                'is_completed' => false,
            ]
        );
    }
}