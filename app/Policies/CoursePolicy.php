<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    /**
     * Determine if the user can view any courses.
     */
    public function viewAny(?User $user): bool
    {
        // Anyone can view published courses
        return true;
    }

    /**
     * Determine if the user can view the course.
     */
    public function view(?User $user, Course $course): bool
    {
        // Anyone can view published courses
        if ($course->isPublished()) {
            return true;
        }

        // Guest users cannot view draft courses
        if (!$user) {
            return false;
        }

        // Admin can view all courses
        if ($user->isAdmin()) {
            return true;
        }

        // Teacher can view their own courses
        if ($user->isTeacher() && $course->teacher_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can create courses.
     */
    public function create(User $user): bool
    {
        // Only teachers and admins can create courses
        return $user->isAdmin() || $user->isTeacher();
    }

    /**
     * Determine if the user can update the course.
     */
    public function update(User $user, Course $course): bool
    {
        // Admin can update any course
        if ($user->isAdmin()) {
            return true;
        }

        // Teacher can only update their own courses
        if ($user->isTeacher() && $course->teacher_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can delete the course.
     */
    public function delete(User $user, Course $course): bool
    {
        // Admin can delete any course
        if ($user->isAdmin()) {
            return true;
        }

        // Teacher can only delete their own courses if no enrollments
        if ($user->isTeacher() && $course->teacher_id === $user->id) {
            return $course->enrollments()->count() === 0;
        }

        return false;
    }

    /**
     * Determine if the user can publish the course.
     */
    public function publish(User $user, Course $course): bool
    {
        return $this->update($user, $course);
    }

    /**
     * Determine if the user can manage course content (sections/lessons).
     */
    public function manageContent(User $user, Course $course): bool
    {
        return $this->update($user, $course);
    }

    /**
     * Determine if the user can view course students.
     */
    public function viewStudents(User $user, Course $course): bool
    {
        // Admin can view students of any course
        if ($user->isAdmin()) {
            return true;
        }

        // Teacher can view students of their own courses
        if ($user->isTeacher() && $course->teacher_id === $user->id) {
            return true;
        }

        return false;
    }
}