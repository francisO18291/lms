<?php

namespace App\Policies;

use App\Models\Lesson;
use App\Models\Course;
use App\Models\User;

class LessonPolicy
{
    /**
     * Determine if the user can view any lessons.
     */
    public function viewAny(?User $user): bool
    {
        // Anyone can view preview lessons
        return true;
    }

    /**
     * Determine if the user can view the lesson.
     */
    public function view(?User $user, Lesson $lesson): bool
    {
        // Anyone can view preview lessons
        if ($lesson->canPreview()) {
            return true;
        }

        // Guest users cannot view non-preview lessons
        if (!$user) {
            return false;
        }

        $course = $lesson->section->course;

        // Admin can view all lessons
        if ($user->isAdmin()) {
            return true;
        }

        // Teacher can view lessons in their own courses
        if ($user->isTeacher() && $course->teacher_id === $user->id) {
            return true;
        }

        // Student can view lessons if enrolled
        if ($user->isStudent() && $user->isEnrolledIn($course)) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can create lessons.
     */
    public function create(User $user, Course $course): bool
    {
        // Admin can create lessons in any course
        if ($user->isAdmin()) {
            return true;
        }

        // Teacher can create lessons in their own courses
        if ($user->isTeacher() && $course->teacher_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can update the lesson.
     */
    public function update(User $user, Lesson $lesson, Course $course): bool
    {
        // Admin can update any lesson
        if ($user->isAdmin()) {
            return true;
        }

        // Teacher can update lessons in their own courses
        if ($user->isTeacher() && $course->teacher_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can delete the lesson.
     */
    public function delete(User $user, Lesson $lesson, Course $course): bool
    {
        // Admin can delete any lesson
        if ($user->isAdmin()) {
            return true;
        }

        // Teacher can delete lessons in their own courses
        if ($user->isTeacher() && $course->teacher_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can mark the lesson as complete.
     */
    public function complete(User $user, Lesson $lesson): bool
    {
        $course = $lesson->section->course;

        // Only enrolled students can mark lessons complete
        return $user->isStudent() && $user->isEnrolledIn($course);
    }
}