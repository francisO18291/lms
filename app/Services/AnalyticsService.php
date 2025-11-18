<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    /**
     * Get platform-wide statistics.
     */
    public function getPlatformStatistics(): array
    {
        return [
            'total_users' => User::count(),
            'total_students' => User::where('role_id', 3)->count(),
            'total_teachers' => User::where('role_id', 2)->count(),
            'total_courses' => Course::count(),
            'published_courses' => Course::where('status', 'published')->count(),
            'total_enrollments' => Enrollment::count(),
            'total_revenue' => Enrollment::sum('price_paid'),
            'active_students' => $this->getActiveStudentsCount(),
            'completion_rate' => $this->calculatePlatformCompletionRate(),
        ];
    }

    /**
     * Get active students (enrolled in last 30 days).
     */
    protected function getActiveStudentsCount(): int
    {
        return Enrollment::where('updated_at', '>=', now()->subDays(30))
            ->distinct('user_id')
            ->count('user_id');
    }

    /**
     * Calculate platform-wide completion rate.
     */
    protected function calculatePlatformCompletionRate(): float
    {
        $totalEnrollments = Enrollment::count();
        
        if ($totalEnrollments === 0) {
            return 0;
        }

        $completedEnrollments = Enrollment::where('progress', 100)->count();
        return round(($completedEnrollments / $totalEnrollments) * 100, 2);
    }

    /**
     * Get revenue analytics.
     */
    public function getRevenueAnalytics(string $period = 'month'): array
    {
        $query = Enrollment::selectRaw('
            DATE_FORMAT(created_at, ?) as period,
            SUM(price_paid) as revenue,
            COUNT(*) as enrollments
        ', [$this->getPeriodFormat($period)])
        ->groupBy('period')
        ->orderBy('period', 'desc')
        ->limit(12);

        $data = $query->get();

        return [
            'total_revenue' => Enrollment::sum('price_paid'),
            'revenue_this_period' => $this->getRevenuThisPeriod($period),
            'average_order_value' => $this->getAverageOrderValue(),
            'revenue_by_period' => $data->toArray(),
            'top_earning_courses' => $this->getTopEarningCourses(5),
        ];
    }

    /**
     * Get format string for SQL date formatting.
     */
    protected function getPeriodFormat(string $period): string
    {
        return match($period) {
            'day' => '%Y-%m-%d',
            'week' => '%Y-%u',
            'month' => '%Y-%m',
            'year' => '%Y',
            default => '%Y-%m',
        };
    }

    /**
     * Get revenue for current period.
     */
    protected function getRevenuThisPeriod(string $period): float
    {
        $query = Enrollment::query();

        return match($period) {
            'day' => $query->whereDate('created_at', now()->toDateString())->sum('price_paid'),
            'week' => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('price_paid'),
            'month' => $query->whereMonth('created_at', now()->month)->sum('price_paid'),
            'year' => $query->whereYear('created_at', now()->year)->sum('price_paid'),
            default => $query->whereMonth('created_at', now()->month)->sum('price_paid'),
        };
    }

    /**
     * Get average order value.
     */
    protected function getAverageOrderValue(): float
    {
        return round(Enrollment::where('price_paid', '>', 0)->avg('price_paid') ?? 0, 2);
    }

    /**
     * Get top earning courses.
     */
    protected function getTopEarningCourses(int $limit = 5): array
    {
        return Course::select('courses.*')
            ->join('enrollments', 'courses.id', '=', 'enrollments.course_id')
            ->selectRaw('SUM(enrollments.price_paid) as total_revenue')
            ->groupBy('courses.id')
            ->orderByDesc('total_revenue')
            ->limit($limit)
            ->get()
            ->map(function($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'revenue' => $course->total_revenue,
                ];
            })
            ->toArray();
    }

    /**
     * Get user growth analytics.
     */
    public function getUserGrowthAnalytics(): array
    {
        $usersByMonth = User::selectRaw('
            DATE_FORMAT(created_at, "%Y-%m") as month,
            COUNT(*) as count
        ')
        ->groupBy('month')
        ->orderBy('month', 'desc')
        ->limit(12)
        ->get();

        return [
            'total_users' => User::count(),
            'new_users_this_month' => User::whereMonth('created_at', now()->month)->count(),
            'growth_rate' => $this->calculateGrowthRate(),
            'users_by_month' => $usersByMonth->toArray(),
            'users_by_role' => $this->getUsersByRole(),
        ];
    }

    /**
     * Calculate user growth rate.
     */
    protected function calculateGrowthRate(): float
    {
        $thisMonth = User::whereMonth('created_at', now()->month)->count();
        $lastMonth = User::whereMonth('created_at', now()->subMonth()->month)->count();

        if ($lastMonth === 0) {
            return 0;
        }

        return round((($thisMonth - $lastMonth) / $lastMonth) * 100, 2);
    }

    /**
     * Get users grouped by role.
     */
    protected function getUsersByRole(): array
    {
        return User::select('roles.name', DB::raw('COUNT(*) as count'))
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->groupBy('roles.name')
            ->pluck('count', 'name')
            ->toArray();
    }

    /**
     * Get course performance analytics.
     */
    public function getCoursePerformanceAnalytics(): array
    {
        return [
            'most_popular_courses' => $this->getMostPopularCourses(10),
            'highest_rated_courses' => $this->getHighestRatedCourses(10),
            'courses_by_completion_rate' => $this->getCoursesByCompletionRate(10),
            'average_course_duration' => $this->getAverageCourseDuration(),
        ];
    }

    /**
     * Get most popular courses by enrollment count.
     */
    protected function getMostPopularCourses(int $limit = 10): array
    {
        return Course::withCount('enrollments')
            ->orderByDesc('enrollments_count')
            ->limit($limit)
            ->get()
            ->map(function($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'enrollments' => $course->enrollments_count,
                ];
            })
            ->toArray();
    }

    /**
     * Get highest rated courses (placeholder - requires rating system).
     */
    protected function getHighestRatedCourses(int $limit = 10): array
    {
        // This would require a ratings table
        return [];
    }

    /**
     * Get courses sorted by completion rate.
     */
    protected function getCoursesByCompletionRate(int $limit = 10): array
    {
        return Course::select('courses.*')
            ->join('enrollments', 'courses.id', '=', 'enrollments.course_id')
            ->selectRaw('
                COUNT(*) as total_enrollments,
                SUM(CASE WHEN enrollments.progress = 100 THEN 1 ELSE 0 END) as completed,
                ROUND((SUM(CASE WHEN enrollments.progress = 100 THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as completion_rate
            ')
            ->groupBy('courses.id')
            ->having('total_enrollments', '>=', 5) // Only courses with 5+ enrollments
            ->orderByDesc('completion_rate')
            ->limit($limit)
            ->get()
            ->map(function($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'completion_rate' => $course->completion_rate,
                    'enrollments' => $course->total_enrollments,
                ];
            })
            ->toArray();
    }

    /**
     * Get average course duration in hours.
     */
    protected function getAverageCourseDuration(): float
    {
        return round(Course::whereNotNull('duration_hours')->avg('duration_hours') ?? 0, 1);
    }

    /**
     * Get teacher performance analytics.
     */
    public function getTeacherPerformanceAnalytics(User $teacher): array
    {
        if (!$teacher->isTeacher()) {
            throw new \Exception('User is not a teacher.');
        }

        $courses = $teacher->taughtCourses;
        $totalEnrollments = $courses->sum(function($course) {
            return $course->studentsCount();
        });

        return [
            'total_courses' => $courses->count(),
            'published_courses' => $courses->where('status', 'published')->count(),
            'total_students' => $totalEnrollments,
            'total_revenue' => Enrollment::whereIn('course_id', $courses->pluck('id'))->sum('price_paid'),
            'average_course_rating' => 0, // Requires rating system
            'completion_rate' => $this->getTeacherCompletionRate($teacher),
            'student_satisfaction' => 0, // Requires feedback system
        ];
    }

    /**
     * Get completion rate for all teacher's courses.
     */
    protected function getTeacherCompletionRate(User $teacher): float
    {
        $courseIds = $teacher->taughtCourses->pluck('id');
        $totalEnrollments = Enrollment::whereIn('course_id', $courseIds)->count();

        if ($totalEnrollments === 0) {
            return 0;
        }

        $completedEnrollments = Enrollment::whereIn('course_id', $courseIds)
            ->where('progress', 100)
            ->count();

        return round(($completedEnrollments / $totalEnrollments) * 100, 2);
    }

    /**
     * Export analytics data to CSV.
     */
    public function exportAnalyticsToCsv(array $data, string $filename): string
    {
        $filepath = storage_path("app/exports/{$filename}");
        $file = fopen($filepath, 'w');

        // Write headers
        if (!empty($data)) {
            fputcsv($file, array_keys($data[0]));
        }

        // Write data
        foreach ($data as $row) {
            fputcsv($file, $row);
        }

        fclose($file);

        return $filepath;
    }
}