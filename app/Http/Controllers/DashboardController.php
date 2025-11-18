<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display dashboard based on user role.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return $this->adminDashboard();
        } elseif ($user->isTeacher()) {
            return $this->teacherDashboard();
        } else {
            return $this->studentDashboard();
        }
    }

    /**
     * Admin dashboard.
     */
    protected function adminDashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_students' => User::where('role_id', 3)->count(),
            'total_teachers' => User::where('role_id', 2)->count(),
            'total_courses' => Course::count(),
            'published_courses' => Course::where('status', 'published')->count(),
            'total_enrollments' => Enrollment::count(),
            'total_revenue' => Enrollment::sum('price_paid'),
            'total_categories' => Category::count(),
        ];

        $recentEnrollments = Enrollment::with(['user', 'course'])
            ->latest()
            ->take(10)
            ->get();

        $popularCourses = Course::withCount('enrollments')
            ->orderBy('enrollments_count', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentEnrollments', 'popularCourses'));
    }

    /**
     * Teacher dashboard.
     */
    protected function teacherDashboard()
    {
        $teacher = Auth::user();

        $stats = [
            'total_courses' => $teacher->taughtCourses()->count(),
            'published_courses' => $teacher->taughtCourses()->where('status', 'published')->count(),
            'draft_courses' => $teacher->taughtCourses()->where('status', 'draft')->count(),
            'total_students' => Enrollment::whereHas('course', function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })->distinct('user_id')->count('user_id'),
            'total_revenue' => Enrollment::whereHas('course', function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })->sum('price_paid'),
        ];

        $myCourses = $teacher->taughtCourses()
            ->withCount('enrollments')
            ->latest()
            ->get();

        $recentEnrollments = Enrollment::with(['user', 'course'])
            ->whereHas('course', function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->latest()
            ->take(10)
            ->get();

        return view('teacher.dashboard', compact('stats', 'myCourses', 'recentEnrollments'));
    }

    /**
     * Student dashboard.
     */
    protected function studentDashboard()
    {
        $student = Auth::user();

        $stats = [
            'enrolled_courses' => $student->enrollments()->count(),
            'completed_courses' => $student->enrollments()->completed()->count(),
            'in_progress_courses' => $student->enrollments()->inProgress()->count(),
            'total_lessons_completed' => $student->lessonProgress()->completed()->count(),
        ];

        $enrolledCourses = $student->enrollments()
            ->with('course.teacher', 'course.category')
            ->latest()
            ->get();

        $continueStudying = $student->enrollments()
            ->inProgress()
            ->with('course.sections.lessons')
            ->take(3)
            ->get();

        $recommendedCourses = Course::published()
            ->whereNotIn('id', $student->enrolledCourses()->pluck('courses.id'))
            ->featured()
            ->take(6)
            ->get();

        return view('student.dashboard', compact('stats', 'enrolledCourses', 'continueStudying', 'recommendedCourses'));
    }
}