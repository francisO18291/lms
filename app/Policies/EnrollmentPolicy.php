<?php

namespace App\Policies;

use App\Models\Enrollment;
use App\Models\User;

class EnrollmentPolicy
{
    /**
     * Determine if the user can view any enrollments.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view enrollments
        // (Controllers will filter based on role)
        return true;
    }

    /**
     * Determine if the user can view the enrollment.
     */
    public function view(User $user, Enrollment $enrollment): bool
    {
        // Admin can view any enrollment
        if ($user->isAdmin()) {
            return true;
        }

        // Teacher can view enrollments for their courses
        if ($user->isTeacher() && $enrollment->course->teacher_id === $user->id) {
            return true;
        }

        // Student can view their own enrollments
        if ($user->isStudent() && $enrollment->user_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can create enrollments.
     */
    public function create(User $user): bool
    {
        // Only students can enroll themselves
        // Admins can enroll students manually
        return $user->isStudent() || $user->isAdmin();
    }

    /**
     * Determine if the user can update the enrollment.
     */
    public function update(User $user, Enrollment $enrollment): bool
    {
        // Only admin can update enrollments
        return $user->isAdmin();
    }

    /**
     * Determine if the user can delete the enrollment.
     */
    public function delete(User $user, Enrollment $enrollment): bool
    {
        // Admin can delete any enrollment
        if ($user->isAdmin()) {
            return true;
        }

        // Student can cancel their own enrollment if progress < 10%
        if ($user->isStudent() && $enrollment->user_id === $user->id) {
            return $enrollment->progress < 10;
        }

        return false;
    }

    /**
     * Determine if the user can view the certificate.
     */
    public function viewCertificate(User $user, Enrollment $enrollment): bool
    {
        // Must be completed and either the student or admin
        if (!$enrollment->isCompleted()) {
            return false;
        }

        return $user->isAdmin() || $enrollment->user_id === $user->id;
    }
}