<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            // Admin User
            [
                'name' => 'Admin FM-LearnHub',
                'email' => 'francismuriithi@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('1@Mur!!th!'),
                'role_id' => 1,
                'phone' => '+25479334567890',
                'bio' => 'System Administrator',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Teacher Users
            [
                'name' => 'John Kamau',
                'email' => 'johnkamau@lms.com',
                'email_verified_at' => now(),
                'password' => Hash::make('123frank'),
                'role_id' => 2,
                'phone' => '+25475434567891',
                'bio' => 'Experienced web development instructor with 10+ years in the industry.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarahjohnson@lms.com',
                'email_verified_at' => now(),
                'password' => Hash::make('123frank'),
                'role_id' => 2,
                'phone' => '+25412334567892',
                'bio' => 'Data Science expert and passionate educator.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mike Wilson',
                'email' => 'mikewilson@lms.com',
                'email_verified_at' => now(),
                'password' => Hash::make('123frank'),
                'role_id' => 2,
                'phone' => '+254712234567893',
                'bio' => 'Mobile app development specialist.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Student Users
            [
                'name' => 'Alice Wambui',
                'email' => 'alicewambui@lms.com',
                'email_verified_at' => now(),
                'password' => Hash::make('123frank'),
                'role_id' => 3,
                'phone' => '+25473334567894',
                'bio' => 'Aspiring web developer',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bob Smith',
                'email' => 'bobsmith@lms.com',
                'email_verified_at' => now(),
                'password' => Hash::make('123frank'),
                'role_id' => 3,
                'phone' => '+25478234567895',
                'bio' => 'Computer science student',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Emma Davis',
                'email' => 'emmadavis@lms.com',
                'email_verified_at' => now(),
                'password' => Hash::make('123frank'),
                'role_id' => 3,
                'phone' => '+25476534567896',
                'bio' => 'Learning data science',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('users')->insert($users);
    }
}