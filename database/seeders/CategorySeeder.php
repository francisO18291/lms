<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Web Development',
                'slug' => 'web-development',
                'description' => 'Learn to build modern websites and web applications',
                'icon' => 'code',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Data Science',
                'slug' => 'data-science',
                'description' => 'Master data analysis, machine learning, and AI',
                'icon' => 'chart-bar',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mobile Development',
                'slug' => 'mobile-development',
                'description' => 'Create iOS and Android applications',
                'icon' => 'mobile',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Design',
                'slug' => 'design',
                'description' => 'UI/UX design and graphic design courses',
                'icon' => 'palette',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Business',
                'slug' => 'business',
                'description' => 'Business strategy, marketing, and entrepreneurship',
                'icon' => 'briefcase',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Programming',
                'slug' => 'programming',
                'description' => 'Learn programming languages and computer science',
                'icon' => 'terminal',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('categories')->insert($categories);
    }
}