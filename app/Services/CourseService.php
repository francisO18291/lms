<?php

namespace App\Services;

use App\Models\Course;
use App\Models\User;
use App\Notifications\CoursePublishedNotification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CourseService
{
    /**
     * Create a new course.
     */
    public function createCourse(array $data, User $teacher): Course
    {
        $data['teacher_id'] = $teacher->id;
        $data['slug'] = Str::slug($data['title']);
        $data['status'] = 'draft';

        // Handle thumbnail upload
        if (isset($data['thumbnail'])) {
            $data['thumbnail'] = $data['thumbnail']->store('thumbnails', 'public');
        }

        return Course::create($data);
    }

    /**
     * Update an existing course.
     */
    public function updateCourse(Course $course, array $data): Course
    {
        $data['slug'] = Str::slug($data['title']);

        // Handle thumbnail upload
        if (isset($data['thumbnail'])) {
            // Delete old thumbnail
            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
            }
            $data['thumbnail'] = $data['thumbnail']->store('thumbnails', 'public');
        }

        $course->update($data);
        return $course->fresh();
    }

    /**
     * Delete a course.
     */
    public function deleteCourse(Course $course): bool
    {
        // Delete thumbnail
        if ($course->thumbnail) {
            Storage::disk('public')->delete($course->thumbnail);
        }

        return $course->delete();
    }

    /**
     * Publish a course.
     */
    public function publishCourse(Course $course): Course
    {
        $course->update(['status' => 'published']);
        
        // Send notification to teacher
        $course->teacher->notify(new CoursePublishedNotification($course));
        
        return $course->fresh();
    }

    /**
     * Unpublish a course.
     */
    public function unpublishCourse(Course $course): Course
    {
        $course->update(['status' => 'draft']);
        return $course->fresh();
    }

    /**
     * Archive a course.
     */
    public function archiveCourse(Course $course): Course
    {
        $course->update(['status' => 'archived']);
        return $course->fresh();
    }

    /**
     * Get course statistics.
     */
    public function getCourseStatistics(Course $course): array
    {
        return [
            'total_students' => $course->studentsCount(),
            'total_lessons' => $course->totalLessons(),
            'total_sections' => $course->sections()->count(),
            'completion_rate' => $this->calculateCompletionRate($course),
            'average_progress' => $this->calculateAverageProgress($course),
            'revenue' => $course->enrollments()->sum('price_paid'),
            'enrollments_this_month' => $course->enrollments()
                ->whereMonth('created_at', now()->month)
                ->count(),
        ];
    }

    /**
     * Calculate course completion rate.
     */
    protected function calculateCompletionRate(Course $course): float
    {
        $totalEnrollments = $course->enrollments()->count();
        
        if ($totalEnrollments === 0) {
            return 0;
        }

        $completedEnrollments = $course->enrollments()
            ->where('progress', 100)
            ->count();

        return round(($completedEnrollments / $totalEnrollments) * 100, 2);
    }

    /**
     * Calculate average progress across all students.
     */
    protected function calculateAverageProgress(Course $course): float
    {
        return round($course->enrollments()->avg('progress') ?? 0, 2);
    }

    /**
     * Get recommended courses for a user.
     */
    public function getRecommendedCourses(User $user, int $limit = 6): \Illuminate\Database\Eloquent\Collection
    {
        // Get user's enrolled course categories
        $enrolledCategories = $user->enrolledCourses()
            ->pluck('category_id')
            ->unique();

        // Get courses from same categories that user hasn't enrolled in
        return Course::published()
            ->whereIn('category_id', $enrolledCategories)
            ->whereNotIn('id', $user->enrolledCourses()->pluck('courses.id'))
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    /**
     * Search courses with advanced filters.
     */
    public function searchCourses(array $filters): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Course::with(['teacher', 'category'])
            ->published()
            ->withCount('enrollments');

        // Apply filters
        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('title', 'like', "%{$filters['search']}%")
                  ->orWhere('description', 'like', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['category'])) {
            $query->whereHas('category', function($q) use ($filters) {
                $q->where('slug', $filters['category']);
            });
        }

        if (!empty($filters['level'])) {
            $query->where('level', $filters['level']);
        }

        if (isset($filters['is_free'])) {
            if ($filters['is_free']) {
                $query->where('price', 0);
            } else {
                $query->where('price', '>', 0);
            }
        }

        // Sorting
        $sortBy = $filters['sort'] ?? 'latest';
        switch ($sortBy) {
            case 'popular':
                $query->orderBy('enrollments_count', 'desc');
                break;
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            default:
                $query->latest();
        }

        return $query->paginate($filters['per_page'] ?? 12);
    }

    /**
     * Duplicate a course.
     */
    public function duplicateCourse(Course $course, User $teacher): Course
    {
        $newCourse = $course->replicate();
        $newCourse->title = $course->title . ' (Copy)';
        $newCourse->slug = Str::slug($newCourse->title);
        $newCourse->teacher_id = $teacher->id;
        $newCourse->status = 'draft';
        $newCourse->save();

        // Duplicate sections and lessons
        foreach ($course->sections as $section) {
            $newSection = $section->replicate();
            $newSection->course_id = $newCourse->id;
            $newSection->save();

            foreach ($section->lessons as $lesson) {
                $newLesson = $lesson->replicate();
                $newLesson->section_id = $newSection->id;
                $newLesson->save();
            }
        }

        return $newCourse;
    }
}