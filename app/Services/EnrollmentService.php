<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Notifications\EnrollmentConfirmation;
use App\Notifications\NewStudentEnrolledNotification;
use Illuminate\Support\Facades\DB;

class EnrollmentService
{
    /**
     * Enroll a user in a course.
     */
    public function enrollUser(User $user, Course $course, float $pricePaid = null): Enrollment
    {
        // Check if already enrolled
        if ($user->isEnrolledIn($course)) {
            throw new \Exception('User is already enrolled in this course.');
        }

        // Check if course is published
        if (!$course->isPublished()) {
            throw new \Exception('This course is not available for enrollment.');
        }

        DB::beginTransaction();

        try {
            // Create enrollment
            $enrollment = Enrollment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'price_paid' => $pricePaid ?? $course->price,
                'progress' => 0,
            ]);

            // Send notifications
            $user->notify(new EnrollmentConfirmation($enrollment));
            $course->teacher->notify(new NewStudentEnrolledNotification($enrollment));

            DB::commit();

            return $enrollment;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Cancel an enrollment.
     */
    public function cancelEnrollment(Enrollment $enrollment): bool
    {
        // Check if cancellation is allowed
        if ($enrollment->progress >= 10) {
            throw new \Exception('Cannot cancel enrollment after 10% progress.');
        }

        return $enrollment->delete();
    }

    /**
     * Get enrollment statistics for a user.
     */
    public function getUserEnrollmentStats(User $user): array
    {
        $enrollments = $user->enrollments;

        return [
            'total' => $enrollments->count(),
            'completed' => $enrollments->where('progress', 100)->count(),
            'in_progress' => $enrollments->where('progress', '>', 0)
                ->where('progress', '<', 100)->count(),
            'not_started' => $enrollments->where('progress', 0)->count(),
            'average_progress' => round($enrollments->avg('progress'), 2),
            'total_spent' => $enrollments->sum('price_paid'),
            'completion_rate' => $this->calculateUserCompletionRate($user),
        ];
    }

    /**
     * Calculate user's completion rate.
     */
    protected function calculateUserCompletionRate(User $user): float
    {
        $total = $user->enrollments()->count();
        
        if ($total === 0) {
            return 0;
        }

        $completed = $user->enrollments()->where('progress', 100)->count();
        return round(($completed / $total) * 100, 2);
    }

    /**
     * Get enrollments that need progress reminders.
     */
    public function getInactiveEnrollments(int $daysInactive = 7): \Illuminate\Database\Eloquent\Collection
    {
        return Enrollment::where('progress', '>', 0)
            ->where('progress', '<', 100)
            ->where('updated_at', '<', now()->subDays($daysInactive))
            ->with(['user', 'course'])
            ->get();
    }

    /**
     * Bulk enroll users in a course.
     */
    public function bulkEnrollUsers(array $userIds, Course $course, float $pricePaid = 0): array
    {
        $results = [
            'success' => [],
            'failed' => [],
        ];

        foreach ($userIds as $userId) {
            try {
                $user = User::findOrFail($userId);
                $enrollment = $this->enrollUser($user, $course, $pricePaid);
                $results['success'][] = [
                    'user_id' => $userId,
                    'enrollment_id' => $enrollment->id,
                ];
            } catch (\Exception $e) {
                $results['failed'][] = [
                    'user_id' => $userId,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Get enrollment report for a course.
     */
    public function getCourseEnrollmentReport(Course $course): array
    {
        $enrollments = $course->enrollments;

        return [
            'total_enrollments' => $enrollments->count(),
            'total_revenue' => $enrollments->sum('price_paid'),
            'completed_count' => $enrollments->where('progress', 100)->count(),
            'average_progress' => round($enrollments->avg('progress'), 2),
            'enrollments_by_month' => $this->getEnrollmentsByMonth($course),
            'completion_rate' => $this->calculateCourseCompletionRate($course),
            'average_time_to_complete' => $this->calculateAverageTimeToComplete($course),
        ];
    }

    /**
     * Get enrollments grouped by month.
     */
    protected function getEnrollmentsByMonth(Course $course): array
    {
        return $course->enrollments()
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->pluck('count', 'month')
            ->toArray();
    }

    /**
     * Calculate course completion rate.
     */
    protected function calculateCourseCompletionRate(Course $course): float
    {
        $total = $course->enrollments()->count();
        
        if ($total === 0) {
            return 0;
        }

        $completed = $course->enrollments()->where('progress', 100)->count();
        return round(($completed / $total) * 100, 2);
    }

    /**
     * Calculate average time to complete course.
     */
    protected function calculateAverageTimeToComplete(Course $course): ?float
    {
        $completedEnrollments = $course->enrollments()
            ->whereNotNull('completed_at')
            ->get();

        if ($completedEnrollments->isEmpty()) {
            return null;
        }

        $totalDays = $completedEnrollments->sum(function ($enrollment) {
            return $enrollment->created_at->diffInDays($enrollment->completed_at);
        });

        return round($totalDays / $completedEnrollments->count(), 1);
    }

    /**
     * Transfer enrollment to another course.
     */
    public function transferEnrollment(Enrollment $enrollment, Course $newCourse): Enrollment
    {
        DB::beginTransaction();

        try {
            // Create new enrollment
            $newEnrollment = Enrollment::create([
                'user_id' => $enrollment->user_id,
                'course_id' => $newCourse->id,
                'price_paid' => 0, // Transfer is free
                'progress' => 0,
            ]);

            // Delete old enrollment
            $enrollment->delete();

            DB::commit();

            return $newEnrollment;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}