<?php

namespace App\Policies;

use App\Models\LessonProgress;
use App\Models\User;

class LessonProgressPolicy
{
    /**
     * Determine if the user can view any lesson progress.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view progress
        return true;
    }

    /**
     * Determine if the user can view the lesson progress.
     */
    public function view(User $user, LessonProgress $lessonProgress): bool
    {
        // Admin can view any progress
        if ($user->isAdmin()) {
            return true;
        }

        $course = $lessonProgress->lesson->section->course;

        // Teacher can view progress in their courses
        if ($user->isTeacher() && $course->teacher_id === $user->id) {
            return true;
        }

        // Student can view their own progress
        if ($user->isStudent() && $lessonProgress->user_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can create lesson progress.
     */
    public function create(User $user): bool
    {
        // Only students can create their own progress
        // This is automatically handled when they start a lesson
        return $user->isStudent();
    }

    /**
     * Determine if the user can update the lesson progress.
     */
    public function update(User $user, LessonProgress $lessonProgress): bool
    {
        // Admin can update any progress
        if ($user->isAdmin()) {
            return true;
        }

        // Student can update their own progress
        if ($user->isStudent() && $lessonProgress->user_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can delete the lesson progress.
     */
    public function delete(User $user, LessonProgress $lessonProgress): bool
    {
        // Only admins can delete progress records
        return $user->isAdmin();
    }

    /**
     * Determine if the user can reset progress.
     */
    public function reset(User $user, LessonProgress $lessonProgress): bool
    {
        // Admin can reset any progress
        if ($user->isAdmin()) {
            return true;
        }

        // Student can reset their own progress
        if ($user->isStudent() && $lessonProgress->user_id === $user->id) {
            return true;
        }

        return false;
    }
}