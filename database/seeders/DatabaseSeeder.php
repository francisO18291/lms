<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            CourseSeeder::class,
            EnrollmentSeeder::class,
        ]);

        $this->command->info('✅ Database seeded successfully!');
        $this->command->newLine();
        $this->command->info('📧 Login Credentials:');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Admin', 'admin@lms.com', 'password'],
                ['Teacher', 'john.teacher@lms.com', 'password'],
                ['Teacher', 'sarah.teacher@lms.com', 'password'],
                ['Teacher', 'mike.teacher@lms.com', 'password'],
                ['Student', 'alice.student@lms.com', 'password'],
                ['Student', 'bob.student@lms.com', 'password'],
                ['Student', 'emma.student@lms.com', 'password'],
            ]
        );
    }
}