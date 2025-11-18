<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    /**
     * Determine if the user can view any categories.
     */
    public function viewAny(?User $user): bool
    {
        // Anyone can view categories
        return true;
    }

    /**
     * Determine if the user can view the category.
     */
    public function view(?User $user, Category $category): bool
    {
        // Anyone can view categories
        return true;
    }

    /**
     * Determine if the user can create categories.
     */
    public function create(User $user): bool
    {
        // Only admins can create categories
        return $user->isAdmin();
    }

    /**
     * Determine if the user can update the category.
     */
    public function update(User $user, Category $category): bool
    {
        // Only admins can update categories
        return $user->isAdmin();
    }

    /**
     * Determine if the user can delete the category.
     */
    public function delete(User $user, Category $category): bool
    {
        // Only admins can delete categories
        // Controller will check if category has courses
        return $user->isAdmin();
    }
}