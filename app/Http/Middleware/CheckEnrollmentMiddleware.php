<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckEnrollmentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Check if user is enrolled in the course before accessing course content.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $course = $request->route('course');

        if (!$user) {
            return redirect()->route('login');
        }

        // Admin and course owner can access
        if ($user->isAdmin() || ($user->isTeacher() && $course->teacher_id === $user->id)) {
            return $next($request);
        }

        // Student must be enrolled
        if ($user->isStudent() && !$user->isEnrolledIn($course)) {
            return redirect()
                ->route('courses.show', $course)
                ->with('error', 'You must enroll in this course to access its content.');
        }

        return $next($request);
    }
}