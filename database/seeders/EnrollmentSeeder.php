<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Enrollments
        $enrollments = [
            // Alice enrolled in Laravel course
            [
                'user_id' => 5, // Alice
                'course_id' => 1, // Laravel for Beginners
                'price_paid' => 49.99,
                'progress' => 50,
                'completed_at' => null,
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(2),
            ],
            // Alice enrolled in Python course
            [
                'user_id' => 5, // Alice
                'course_id' => 2, // Python for Data Science
                'price_paid' => 79.99,
                'progress' => 0,
                'completed_at' => null,
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
            // Bob enrolled in Laravel course
            [
                'user_id' => 6, // Bob
                'course_id' => 1, // Laravel for Beginners
                'price_paid' => 49.99,
                'progress' => 100,
                'completed_at' => now()->subDays(1),
                'created_at' => now()->subDays(15),
                'updated_at' => now()->subDays(1),
            ],
            // Emma enrolled in React Native course
            [
                'user_id' => 7, // Emma
                'course_id' => 3, // React Native Masterclass
                'price_paid' => 89.99,
                'progress' => 25,
                'completed_at' => null,
                'created_at' => now()->subDays(7),
                'updated_at' => now()->subDays(3),
            ],
            // Emma enrolled in Python course
            [
                'user_id' => 7, // Emma
                'course_id' => 2, // Python for Data Science
                'price_paid' => 79.99,
                'progress' => 10,
                'completed_at' => null,
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(1),
            ],
        ];

        DB::table('enrollments')->insert($enrollments);

        // Lesson Progress for Alice in Laravel course (50% progress)
        $lessonProgress = [
            // Alice completed first 2 lessons
            [
                'user_id' => 5,
                'lesson_id' => 1, // Introduction to Laravel
                'is_completed' => true,
                'completed_at' => now()->subDays(9),
                'created_at' => now()->subDays(9),
                'updated_at' => now()->subDays(9),
            ],
            [
                'user_id' => 5,
                'lesson_id' => 2, // Installation and Setup
                'is_completed' => true,
                'completed_at' => now()->subDays(8),
                'created_at' => now()->subDays(8),
                'updated_at' => now()->subDays(8),
            ],
            // Bob completed all lessons in Laravel course
            [
                'user_id' => 6,
                'lesson_id' => 1,
                'is_completed' => true,
                'completed_at' => now()->subDays(14),
                'created_at' => now()->subDays(14),
                'updated_at' => now()->subDays(14),
            ],
            [
                'user_id' => 6,
                'lesson_id' => 2,
                'is_completed' => true,
                'completed_at' => now()->subDays(13),
                'created_at' => now()->subDays(13),
                'updated_at' => now()->subDays(13),
            ],
            [
                'user_id' => 6,
                'lesson_id' => 3,
                'is_completed' => true,
                'completed_at' => now()->subDays(5),
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
            [
                'user_id' => 6,
                'lesson_id' => 4,
                'is_completed' => true,
                'completed_at' => now()->subDays(1),
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],
            // Emma completed first lesson in React Native
            [
                'user_id' => 7,
                'lesson_id' => 7,
                'is_completed' => true,
                'completed_at' => now()->subDays(6),
                'created_at' => now()->subDays(6),
                'updated_at' => now()->subDays(6),
            ],
            // Emma started first lesson in Python course
            [
                'user_id' => 7,
                'lesson_id' => 5,
                'is_completed' => false,
                'completed_at' => null,
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(1),
            ],
        ];

        DB::table('lesson_progress')->insert($lessonProgress);
    }
}