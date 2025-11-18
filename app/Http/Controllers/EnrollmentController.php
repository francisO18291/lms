<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    /**
     * Display user's enrollments.
     */
    public function index()
    {
        $user = Auth::user();
        
        $enrollments = $user->enrollments()
            ->with(['course.teacher', 'course.category'])
            ->latest()
            ->get();

        $stats = [
            'total' => $enrollments->count(),
            'completed' => $enrollments->where('progress', 100)->count(),
            'in_progress' => $enrollments->where('progress', '>', 0)->where('progress', '<', 100)->count(),
            'not_started' => $enrollments->where('progress', 0)->count(),
        ];

        return view('enrollments.index', compact('enrollments', 'stats'));
    }

    /**
     * Enroll user in a course.
     */
    public function store(Request $request, Course $course)
    {
        $user = Auth::user();

        // Check if already enrolled
        if ($user->isEnrolledIn($course)) {
            return back()->with('error', 'You are already enrolled in this course!');
        }

        // Check if course is published
        if (!$course->isPublished()) {
            return back()->with('error', 'This course is not available for enrollment.');
        }

        try {
            DB::beginTransaction();

            // Create enrollment
            Enrollment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'price_paid' => $course->price,
                'progress' => 0,
            ]);

            // Here you would integrate payment processing
            // For now, we're assuming free enrollment or payment is handled separately

            DB::commit();

            return redirect()
                ->route('courses.show', $course)
                ->with('success', 'Successfully enrolled in the course!');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to enroll in course. Please try again.');
        }
    }

    /**
     * Show enrollment details.
     */
    public function show(Enrollment $enrollment)
    {
        //$this->authorize('view', $enrollment);

        $enrollment->load(['course.sections.lessons', 'user']);

        // Get completed lessons for this enrollment
        $completedLessons = $enrollment->user
            ->lessonProgress()
            ->whereHas('lesson.section', function($query) use ($enrollment) {
                $query->where('course_id', $enrollment->course_id);
            })
            ->where('is_completed', true)
            ->count();

        $totalLessons = $enrollment->course->totalLessons();

        return view('enrollments.show', compact('enrollment', 'completedLessons', 'totalLessons'));
    }

    /**
     * Cancel enrollment (if allowed).
     */
    public function destroy(Enrollment $enrollment)
    {
        //$this->authorize('delete', $enrollment);

        // Only allow cancellation if progress is less than 10%
        if ($enrollment->progress >= 10) {
            return back()->with('error', 'Cannot cancel enrollment after 10% progress.');
        }

        $enrollment->delete();

        return redirect()
            ->route('enrollments.index')
            ->with('success', 'Enrollment cancelled successfully.');
    }

    /**
     * Get certificate for completed course.
     */
    public function certificate(Enrollment $enrollment)
    {
        //$this->authorize('view', $enrollment);

        if (!$enrollment->isCompleted()) {
            return back()->with('error', 'Course must be completed to get certificate.');
        }

        // Here you would generate a PDF certificate
        // For now, return a view
        return view('enrollments.certificate', compact('enrollment'));
    }
}