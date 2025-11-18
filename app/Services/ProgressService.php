<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\User;
use App\Notifications\CourseCompletedNotification;

class ProgressService
{
    /**
     * Mark a lesson as complete.
     */
    public function completeLesson(User $user, Lesson $lesson): LessonProgress
    {
        $course = $lesson->section->course;

        // Check if user is enrolled
        if (!$user->isEnrolledIn($course)) {
            throw new \Exception('User must be enrolled in the course to mark lessons as complete.');
        }

        // Get or create progress
        $progress = LessonProgress::getOrCreateProgress($user->id, $lesson->id);
        $progress->markAsCompleted();

        // Check if course is now complete
        $this->checkCourseCompletion($user, $course);

        return $progress;
    }

    /**
     * Mark a lesson as incomplete.
     */
    public function incompleteLesson(User $user, Lesson $lesson): LessonProgress
    {
        $progress = LessonProgress::where('user_id', $user->id)
            ->where('lesson_id', $lesson->id)
            ->firstOrFail();

        $progress->markAsIncomplete();

        return $progress;
    }

    /**
     * Check if course is complete and send notification.
     */
    protected function checkCourseCompletion(User $user, Course $course): void
    {
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($enrollment && $enrollment->progress === 100 && !$enrollment->completed_at) {
            $enrollment->markAsCompleted();
            $user->notify(new CourseCompletedNotification($enrollment));
        }
    }

    /**
     * Get user's progress in a course.
     */
    public function getCourseProgress(User $user, Course $course): array
    {
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            return [
                'enrolled' => false,
                'progress' => 0,
                'completed_lessons' => 0,
                'total_lessons' => $course->totalLessons(),
            ];
        }

        $completedLessons = LessonProgress::where('user_id', $user->id)
            ->whereHas('lesson.section', function($query) use ($course) {
                $query->where('course_id', $course->id);
            })
            ->where('is_completed', true)
            ->count();

        $totalLessons = $course->totalLessons();

        return [
            'enrolled' => true,
            'progress' => $enrollment->progress,
            'completed_lessons' => $completedLessons,
            'total_lessons' => $totalLessons,
            'remaining_lessons' => $totalLessons - $completedLessons,
            'is_completed' => $enrollment->isCompleted(),
            'completed_at' => $enrollment->completed_at,
            'last_activity' => $enrollment->updated_at,
        ];
    }

    /**
     * Get user's overall learning progress.
     */
    public function getUserOverallProgress(User $user): array
    {
        $enrollments = $user->enrollments()->with('course')->get();

        $totalLessons = 0;
        $completedLessons = 0;

        foreach ($enrollments as $enrollment) {
            $courseLessons = $enrollment->course->totalLessons();
            $totalLessons += $courseLessons;
            $completedLessons += ($courseLessons * $enrollment->progress) / 100;
        }

        return [
            'total_courses' => $enrollments->count(),
            'completed_courses' => $enrollments->where('progress', 100)->count(),
            'in_progress_courses' => $enrollments->where('progress', '>', 0)
                ->where('progress', '<', 100)->count(),
            'total_lessons' => $totalLessons,
            'completed_lessons' => (int) $completedLessons,
            'overall_progress' => $totalLessons > 0 
                ? round(($completedLessons / $totalLessons) * 100, 2) 
                : 0,
            'learning_streak' => $this->calculateLearningStreak($user),
        ];
    }

    /**
     * Calculate user's learning streak (consecutive days).
     */
    protected function calculateLearningStreak(User $user): int
    {
        $progress = LessonProgress::where('user_id', $user->id)
            ->where('is_completed', true)
            ->orderBy('completed_at', 'desc')
            ->get();

        if ($progress->isEmpty()) {
            return 0;
        }

        $streak = 1;
        $currentDate = $progress->first()->completed_at->startOfDay();

        foreach ($progress->skip(1) as $item) {
            $itemDate = $item->completed_at->startOfDay();
            $daysDiff = $currentDate->diffInDays($itemDate);

            if ($daysDiff === 1) {
                $streak++;
                $currentDate = $itemDate;
            } elseif ($daysDiff > 1) {
                break;
            }
        }

        return $streak;
    }

    /**
     * Get next lesson for a user in a course.
     */
    public function getNextLesson(User $user, Course $course): ?Lesson
    {
        // Get all lessons in order
        $lessons = $course->sections()
            ->with('lessons')
            ->orderBy('order')
            ->get()
            ->pluck('lessons')
            ->flatten()
            ->sortBy('order');

        // Find first incomplete lesson
        foreach ($lessons as $lesson) {
            if (!$user->hasCompletedLesson($lesson)) {
                return $lesson;
            }
        }

        return null; // All lessons completed
    }

    /**
     * Reset course progress for a user.
     */
    public function resetCourseProgress(User $user, Course $course): void
    {
        // Delete all lesson progress for this course
        LessonProgress::where('user_id', $user->id)
            ->whereHas('lesson.section', function($query) use ($course) {
                $query->where('course_id', $course->id);
            })
            ->delete();

        // Reset enrollment progress
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($enrollment) {
            $enrollment->update([
                'progress' => 0,
                'completed_at' => null,
            ]);
        }
    }

    /**
     * Get course progress report.
     */
    public function getCourseProgressReport(Course $course): array
    {
        $enrollments = $course->enrollments;

        $progressRanges = [
            '0-25%' => 0,
            '26-50%' => 0,
            '51-75%' => 0,
            '76-99%' => 0,
            '100%' => 0,
        ];

        foreach ($enrollments as $enrollment) {
            if ($enrollment->progress === 0) {
                $progressRanges['0-25%']++;
            } elseif ($enrollment->progress <= 25) {
                $progressRanges['0-25%']++;
            } elseif ($enrollment->progress <= 50) {
                $progressRanges['26-50%']++;
            } elseif ($enrollment->progress <= 75) {
                $progressRanges['51-75%']++;
            } elseif ($enrollment->progress < 100) {
                $progressRanges['76-99%']++;
            } else {
                $progressRanges['100%']++;
            }
        }

        return [
            'total_students' => $enrollments->count(),
            'progress_distribution' => $progressRanges,
            'average_progress' => round($enrollments->avg('progress'), 2),
            'completion_rate' => $enrollments->count() > 0
                ? round(($progressRanges['100%'] / $enrollments->count()) * 100, 2)
                : 0,
        ];
    }

    /**
     * Get lesson completion statistics.
     */
    public function getLessonStatistics(Lesson $lesson): array
    {
        $totalProgress = LessonProgress::where('lesson_id', $lesson->id)->count();
        $completed = LessonProgress::where('lesson_id', $lesson->id)
            ->where('is_completed', true)
            ->count();

        return [
            'total_students_started' => $totalProgress,
            'total_students_completed' => $completed,
            'completion_rate' => $totalProgress > 0 
                ? round(($completed / $totalProgress) * 100, 2) 
                : 0,
        ];
    }
}