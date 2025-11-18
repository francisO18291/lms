<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Course 1: Laravel for Beginners
        $course1 = DB::table('courses')->insertGetId([
            'teacher_id' => 2, // John Teacher
            'category_id' => 1, // Web Development
            'title' => 'Laravel for Beginners',
            'slug' => 'laravel-for-beginners',
            'description' => 'Learn Laravel framework from scratch and build modern web applications.',
            'requirements' => 'Basic PHP knowledge, HTML, CSS',
            'learning_outcomes' => 'Build full-stack web applications, Understand MVC architecture, Work with databases using Eloquent ORM',
            'price' => 49.99,
            'level' => 'beginner',
            'status' => 'published',
            'duration_hours' => 15,
            'is_featured' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Sections for Course 1
        $section1_1 = DB::table('sections')->insertGetId([
            'course_id' => $course1,
            'title' => 'Getting Started',
            'order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $section1_2 = DB::table('sections')->insertGetId([
            'course_id' => $course1,
            'title' => 'Laravel Fundamentals',
            'order' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Lessons for Course 1
        DB::table('lessons')->insert([
            [
                'section_id' => $section1_1,
                'title' => 'Introduction to Laravel',
                'slug' => 'introduction-to-laravel',
                'content' => 'Welcome to Laravel! In this lesson, we will cover what Laravel is and why it is popular.',
                'type' => 'video',
                'video_url' => 'https://example.com/video1.mp4',
                'duration_minutes' => 15,
                'order' => 1,
                'is_preview' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'section_id' => $section1_1,
                'title' => 'Installation and Setup',
                'slug' => 'installation-and-setup',
                'content' => 'Learn how to install Laravel and set up your development environment.',
                'type' => 'video',
                'video_url' => 'https://example.com/video2.mp4',
                'duration_minutes' => 20,
                'order' => 2,
                'is_preview' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'section_id' => $section1_2,
                'title' => 'Routing Basics',
                'slug' => 'routing-basics',
                'content' => 'Understanding Laravel routing system and how to create routes.',
                'type' => 'video',
                'video_url' => 'https://example.com/video3.mp4',
                'duration_minutes' => 25,
                'order' => 1,
                'is_preview' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'section_id' => $section1_2,
                'title' => 'Controllers and Views',
                'slug' => 'controllers-and-views',
                'content' => 'Learn about MVC pattern, controllers, and blade templates.',
                'type' => 'video',
                'video_url' => 'https://example.com/video4.mp4',
                'duration_minutes' => 30,
                'order' => 2,
                'is_preview' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Course 2: Python Data Science
        $course2 = DB::table('courses')->insertGetId([
            'teacher_id' => 3, // Sarah Johnson
            'category_id' => 2, // Data Science
            'title' => 'Python for Data Science',
            'slug' => 'python-for-data-science',
            'description' => 'Master data analysis and visualization with Python, Pandas, and NumPy.',
            'requirements' => 'Basic programming knowledge',
            'learning_outcomes' => 'Data manipulation with Pandas, Data visualization, Statistical analysis, Machine learning basics',
            'price' => 79.99,
            'level' => 'intermediate',
            'status' => 'published',
            'duration_hours' => 20,
            'is_featured' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $section2_1 = DB::table('sections')->insertGetId([
            'course_id' => $course2,
            'title' => 'Python Basics Review',
            'order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $section2_2 = DB::table('sections')->insertGetId([
            'course_id' => $course2,
            'title' => 'Data Analysis with Pandas',
            'order' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('lessons')->insert([
            [
                'section_id' => $section2_1,
                'title' => 'Python Refresher',
                'slug' => 'python-refresher',
                'content' => 'Quick review of Python fundamentals for data science.',
                'type' => 'video',
                'video_url' => 'https://example.com/video5.mp4',
                'duration_minutes' => 18,
                'order' => 1,
                'is_preview' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'section_id' => $section2_2,
                'title' => 'Introduction to Pandas',
                'slug' => 'introduction-to-pandas',
                'content' => 'Learn the basics of Pandas library for data manipulation.',
                'type' => 'video',
                'video_url' => 'https://example.com/video6.mp4',
                'duration_minutes' => 35,
                'order' => 1,
                'is_preview' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Course 3: React Native Masterclass
        $course3 = DB::table('courses')->insertGetId([
            'teacher_id' => 4, // Mike Wilson
            'category_id' => 3, // Mobile Development
            'title' => 'React Native Masterclass',
            'slug' => 'react-native-masterclass',
            'description' => 'Build cross-platform mobile apps with React Native.',
            'requirements' => 'JavaScript and React knowledge',
            'learning_outcomes' => 'Build iOS and Android apps, Navigation and routing, API integration, Publishing apps',
            'price' => 89.99,
            'level' => 'advanced',
            'status' => 'published',
            'duration_hours' => 25,
            'is_featured' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $section3_1 = DB::table('sections')->insertGetId([
            'course_id' => $course3,
            'title' => 'React Native Setup',
            'order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('lessons')->insert([
            [
                'section_id' => $section3_1,
                'title' => 'Environment Setup',
                'slug' => 'environment-setup',
                'content' => 'Setting up React Native development environment.',
                'type' => 'video',
                'video_url' => 'https://example.com/video7.mp4',
                'duration_minutes' => 22,
                'order' => 1,
                'is_preview' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Course 4: Draft Course
        DB::table('courses')->insert([
            'teacher_id' => 2,
            'category_id' => 6,
            'title' => 'Advanced JavaScript Concepts',
            'slug' => 'advanced-javascript-concepts',
            'description' => 'Deep dive into JavaScript closures, prototypes, and async programming.',
            'requirements' => 'Intermediate JavaScript knowledge',
            'learning_outcomes' => 'Master closures and scope, Understand prototypal inheritance, Async/await patterns',
            'price' => 59.99,
            'level' => 'advanced',
            'status' => 'draft',
            'duration_hours' => 12,
            'is_featured' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}