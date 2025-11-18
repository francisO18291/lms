<?php

namespace App\Policies;

use App\Models\Section;
use App\Models\Course;
use App\Models\User;

class SectionPolicy
{
    /**
     * Determine if the user can view any sections.
     */
    public function viewAny(?User $user): bool
    {
        // Anyone can view sections (in context of viewing a course)
        return true;
    }

    /**
     * Determine if the user can view the section.
     */
    public function view(?User $user, Section $section): bool
    {
        // Viewing sections is controlled by course policy
        return true;
    }

    /**
     * Determine if the user can create sections.
     */
    public function create(User $user, Course $course): bool
    {
        // Admin can create sections in any course
        if ($user->isAdmin()) {
            return true;
        }

        // Teacher can create sections in their own courses
        if ($user->isTeacher() && $course->teacher_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can update the section.
     */
    public function update(User $user, Section $section): bool
    {
        $course = $section->course;

        // Admin can update any section
        if ($user->isAdmin()) {
            return true;
        }

        // Teacher can update sections in their own courses
        if ($user->isTeacher() && $course->teacher_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can delete the section.
     */
    public function delete(User $user, Section $section): bool
    {
        $course = $section->course;

        // Admin can delete any section
        if ($user->isAdmin()) {
            return true;
        }

        // Teacher can delete sections in their own courses
        if ($user->isTeacher() && $course->teacher_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can reorder sections.
     */
    public function reorder(User $user, Course $course): bool
    {
        // Admin can reorder sections in any course
        if ($user->isAdmin()) {
            return true;
        }

        // Teacher can reorder sections in their own courses
        if ($user->isTeacher() && $course->teacher_id === $user->id) {
            return true;
        }

        return false;
    }
}