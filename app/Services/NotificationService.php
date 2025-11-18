<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Notifications\CourseProgressReminderNotification;
use Illuminate\Support\Facades\Notification;

class NotificationService
{
    /**
     * Send progress reminders to inactive students.
     */
    public function sendProgressReminders(int $daysInactive = 7): array
    {
        $enrollmentService = new EnrollmentService();
        $inactiveEnrollments = $enrollmentService->getInactiveEnrollments($daysInactive);

        $sent = 0;
        $failed = 0;

        foreach ($inactiveEnrollments as $enrollment) {
            try {
                $enrollment->user->notify(new CourseProgressReminderNotification($enrollment));
                $sent++;
            } catch (\Exception $e) {
                $failed++;
                \Log::error('Failed to send progress reminder', [
                    'enrollment_id' => $enrollment->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return [
            'total_processed' => $inactiveEnrollments->count(),
            'sent' => $sent,
            'failed' => $failed,
        ];
    }

    /**
     * Send bulk notification to all students in a course.
     */
    public function sendCourseAnnouncement(Course $course, string $title, string $message): array
    {
        $students = $course->students;

        if ($students->isEmpty()) {
            return [
                'total' => 0,
                'sent' => 0,
                'failed' => 0,
            ];
        }

        $sent = 0;
        $failed = 0;

        foreach ($students as $student) {
            try {
                // Send custom notification (you'd create a CourseAnnouncementNotification class)
                // $student->notify(new CourseAnnouncementNotification($course, $title, $message));
                $sent++;
            } catch (\Exception $e) {
                $failed++;
                \Log::error('Failed to send course announcement', [
                    'course_id' => $course->id,
                    'user_id' => $student->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return [
            'total' => $students->count(),
            'sent' => $sent,
            'failed' => $failed,
        ];
    }

    /**
     * Send welcome email to new users.
     */
    public function sendWelcomeEmail(User $user): bool
    {
        try {
            $user->notify(new \App\Notifications\WelcomeNotification());
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to send welcome email', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send notification to teachers about their monthly stats.
     */
    public function sendMonthlyTeacherReport(User $teacher): array
    {
        if (!$teacher->isTeacher()) {
            throw new \Exception('User is not a teacher.');
        }

        $courses = $teacher->taughtCourses;
        $enrollmentsThisMonth = Enrollment::whereHas('course', function($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->whereMonth('created_at', now()->month)->count();

        $revenueThisMonth = Enrollment::whereHas('course', function($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->whereMonth('created_at', now()->month)->sum('price_paid');

        $data = [
            'total_courses' => $courses->count(),
            'enrollments_this_month' => $enrollmentsThisMonth,
            'revenue_this_month' => $revenueThisMonth,
            'total_students' => $courses->sum(function($course) {
                return $course->studentsCount();
            }),
        ];

        // You would create a MonthlyTeacherReportNotification class
        // $teacher->notify(new MonthlyTeacherReportNotification($data));

        return $data;
    }

    /**
     * Get notification preferences for a user.
     */
    public function getUserNotificationPreferences(User $user): array
    {
        // This assumes you have a notification_preferences JSON column on users table
        // or a separate table for preferences
        return $user->notification_preferences ?? [
            'email_progress_reminders' => true,
            'email_course_updates' => true,
            'email_new_courses' => true,
            'email_certificates' => true,
            'email_marketing' => false,
        ];
    }

    /**
     * Update user notification preferences.
     */
    public function updateNotificationPreferences(User $user, array $preferences): bool
    {
        try {
            $user->update([
                'notification_preferences' => $preferences,
            ]);
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to update notification preferences', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send test notification.
     */
    public function sendTestNotification(User $user): bool
    {
        try {
            $user->notify(new \Illuminate\Notifications\Messages\MailMessage([
                'subject' => 'Test Notification',
                'greeting' => 'Hello!',
                'line' => 'This is a test notification from the LMS system.',
            ]));
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to send test notification', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get notification statistics for admin.
     */
    public function getNotificationStatistics(): array
    {
        // This would require a notifications table
        return [
            'total_sent_today' => 0, // Query from notifications table
            'total_sent_this_week' => 0,
            'total_sent_this_month' => 0,
            'failed_notifications' => 0,
            'notification_types' => [
                'welcome' => 0,
                'enrollment' => 0,
                'completion' => 0,
                'reminder' => 0,
            ],
        ];
    }

    /**
     * Queue bulk notifications for better performance.
     */
    public function queueBulkNotifications(array $userIds, $notificationClass, array $data = []): void
    {
        $users = User::whereIn('id', $userIds)->get();

        foreach ($users->chunk(100) as $chunk) {
            Notification::send($chunk, new $notificationClass(...array_values($data)));
        }
    }
}