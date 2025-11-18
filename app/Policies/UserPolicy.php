<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine if the user can view any users.
     */
    public function viewAny(User $user): bool
    {
        // Admins and teachers can view users
        return $user->isAdmin() || $user->isTeacher();
    }

    /**
     * Determine if the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Admin can view any user
        if ($user->isAdmin()) {
            return true;
        }

        // Teachers can view students enrolled in their courses
        if ($user->isTeacher() && $model->isStudent()) {
            return $model->enrollments()
                ->whereHas('course', function($query) use ($user) {
                    $query->where('teacher_id', $user->id);
                })
                ->exists();
        }

        // Users can view their own profile
        return $user->id === $model->id;
    }

    /**
     * Determine if the user can create users.
     */
    public function create(User $user): bool
    {
        // Only admins can create users
        return $user->isAdmin();
    }

    /**
     * Determine if the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Admin can update any user
        if ($user->isAdmin()) {
            return true;
        }

        // Users can update their own profile
        return $user->id === $model->id;
    }

    /**
     * Determine if the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Only admins can delete users
        // Cannot delete yourself
        return $user->isAdmin() && $user->id !== $model->id;
    }

    /**
     * Determine if the user can change roles.
     */
    public function changeRole(User $user, User $model): bool
    {
        // Only admins can change roles
        // Cannot change your own role
        return $user->isAdmin() && $user->id !== $model->id;
    }

    /**
     * Determine if the user can view students.
     */
    public function viewStudents(User $user): bool
    {
        // Admins and teachers can view students
        return $user->isAdmin() || $user->isTeacher();
    }

    /**
     * Determine if the user can view teachers.
     */
    public function viewTeachers(User $user): bool
    {
        // Only admins can view teachers list
        return $user->isAdmin();
    }
}