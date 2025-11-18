<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    /**
     * Display a listing of students (Admin/Teacher only).
     */
    public function index(Request $request)
    {
        //$this->authorize('viewAny', User::class);

        $query = User::where('role_id', 3) // Students only
            ->withCount('enrollments');

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // For teachers, show only students enrolled in their courses
        if (Auth::user()->isTeacher()) {
            $teacherId = Auth::id();
            $query->whereHas('enrollments.course', function($q) use ($teacherId) {
                $q->where('teacher_id', $teacherId);
            });
        }

        $students = $query->paginate(20);

        return view('students.index', compact('students'));
    }

    /**
     * Display student profile with enrollments.
     */
    public function show(User $user)
    {
        //$this->authorize('view', $user);

        // Ensure user is a student
        if (!$user->isStudent()) {
            abort(404);
        }

        $enrollments = $user->enrollments()
            ->with(['course.teacher', 'course.category'])
            ->latest()
            ->get();

        // For teachers, filter enrollments to show only their courses
        if (Auth::user()->isTeacher()) {
            $teacherId = Auth::id();
            $enrollments = $enrollments->filter(function($enrollment) use ($teacherId) {
                return $enrollment->course->teacher_id === $teacherId;
            });
        }

        $stats = [
            'total_enrollments' => $enrollments->count(),
            'completed' => $enrollments->where('progress', 100)->count(),
            'in_progress' => $enrollments->where('progress', '>', 0)->where('progress', '<', 100)->count(),
            'total_completed_lessons' => $user->lessonProgress()->completed()->count(),
        ];

        return view('students.show', compact('user', 'enrollments', 'stats'));
    }

    /**
     * Show student progress in a specific course.
     */
    public function courseProgress(User $user, $courseId)
    {
        //$this->authorize('view', $user);

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->with(['course.sections.lessons'])
            ->firstOrFail();

        // For teachers, ensure they own the course
        if (Auth::user()->isTeacher() && $enrollment->course->teacher_id !== Auth::id()) {
            abort(403);
        }

        // Get lesson progress
        $lessonProgress = $user->lessonProgress()
            ->whereHas('lesson.section', function($query) use ($courseId) {
                $query->where('course_id', $courseId);
            })
            ->with('lesson')
            ->get()
            ->keyBy('lesson_id');

        return view('students.course-progress', compact('user', 'enrollment', 'lessonProgress'));
    }

    /**
     * Update student status (Admin only).
     */
    public function updateStatus(Request $request, User $user)
    {
        //$this->authorize('update', $user);

        $validated = $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $user->update($validated);

        return back()->with('success', 'Student status updated successfully!');
    }
}