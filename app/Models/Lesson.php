<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Lesson extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'section_id',
        'title',
        'slug',
        'content',
        'type',
        'video_url',
        'duration_minutes',
        'order',
        'is_preview',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'duration_minutes' => 'integer',
            'order' => 'integer',
            'is_preview' => 'boolean',
        ];
    }

    /**
     * Get the section that owns this lesson.
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * Get the course through section.
     */
    public function course()
    {
        return $this->section->course();
    }

    /**
     * Get all progress records for this lesson.
     */
    public function progress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    /**
     * Get users who completed this lesson.
     */
    public function completedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'lesson_progress')
            ->wherePivot('is_completed', true)
            ->withPivot('completed_at')
            ->withTimestamps();
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Check if lesson is video type.
     */
    public function isVideo(): bool
    {
        return $this->type === 'video';
    }

    /**
     * Check if lesson is text type.
     */
    public function isText(): bool
    {
        return $this->type === 'text';
    }

    /**
     * Check if lesson is quiz type.
     */
    public function isQuiz(): bool
    {
        return $this->type === 'quiz';
    }

    /**
     * Check if lesson is assignment type.
     */
    public function isAssignment(): bool
    {
        return $this->type === 'assignment';
    }

    /**
     * Check if lesson can be previewed without enrollment.
     */
    public function canPreview(): bool
    {
        return $this->is_preview;
    }

    /**
     * Get formatted duration (e.g., "1h 30m" or "45m").
     */
    public function formattedDuration(): string
    {
        if (!$this->duration_minutes) {
            return 'N/A';
        }

        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;

        if ($hours > 0) {
            return $minutes > 0 ? "{$hours}h {$minutes}m" : "{$hours}h";
        }

        return "{$minutes}m";
    }
}