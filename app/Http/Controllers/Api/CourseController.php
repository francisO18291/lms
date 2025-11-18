<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use App\Http\Resources\CourseCollection;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CourseController extends Controller
{
    /**
     * Display a listing of published courses.
     */
    public function index(Request $request): CourseCollection
    {
        $query = Course::with(['teacher', 'category'])
            ->published()
            ->withCount('enrollments');

        // Filter by category
        if ($request->has('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by level
        if ($request->has('level')) {
            $query->where('level', $request->level);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort', 'latest');
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

        $courses = $query->paginate($request->get('per_page', 15));

        return new CourseCollection($courses);
    }

    /**
     * Display the specified course.
     */
    public function show(Course $course): CourseResource
    {
        $course->load(['teacher', 'category', 'sections.lessons']);
        
        return new CourseResource($course);
    }

    /**
     * Get course curriculum (sections and lessons).
     */
    public function curriculum(Course $course): JsonResponse
    {
        $course->load(['sections.lessons']);
        
        return response()->json([
            'course_id' => $course->id,
            'course_title' => $course->title,
            'sections' => $course->sections->map(function ($section) {
                return [
                    'id' => $section->id,
                    'title' => $section->title,
                    'order' => $section->order,
                    'lessons_count' => $section->lessons->count(),
                    'total_duration' => $section->totalDuration(),
                    'lessons' => $section->lessons->map(function ($lesson) {
                        return [
                            'id' => $lesson->id,
                            'title' => $lesson->title,
                            'type' => $lesson->type,
                            'duration' => $lesson->duration_minutes,
                            'is_preview' => $lesson->is_preview,
                            'order' => $lesson->order,
                        ];
                    }),
                ];
            }),
            'total_lessons' => $course->totalLessons(),
        ]);
    }

    /**
     * Public index for browsing (no auth required).
     */
    public function publicIndex(Request $request): CourseCollection
    {
        return $this->index($request);
    }

    /**
     * Public show for viewing (no auth required).
     */
    public function publicShow(Course $course): CourseResource
    {
        if (!$course->isPublished()) {
            abort(404, 'Course not found');
        }
        
        return $this->show($course);
    }
}