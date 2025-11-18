<?php

namespace App\Notifications;

use App\Models\Enrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CourseCompletedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Enrollment $enrollment
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $course = $this->enrollment->course;

        return (new MailMessage)
            ->subject('🎉 Congratulations! You\'ve completed ' . $course->title)
            ->greeting('Congratulations, ' . $notifiable->name . '!')
            ->line('You have successfully completed **' . $course->title . '**! 🎓')
            ->line('This is a significant achievement and we\'re proud of your dedication and hard work.')
            ->line('**Course Summary:**')
            ->line('• Course: ' . $course->title)
            ->line('• Instructor: ' . $course->teacher->name)
            ->line('• Completion Date: ' . $this->enrollment->completed_at->format('F d, Y'))
            ->line('Your certificate of completion is now available!')
            ->action('Download Certificate', route('enrollments.certificate', $this->enrollment))
            ->line('Keep up the great work and continue your learning journey!')
            ->line('**What\'s next?**')
            ->line('Explore more courses to expand your knowledge and skills.')
            ->action('Browse More Courses', url('/courses'))
            ->salutation('Congratulations again, ' . config('app.name') . ' Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'enrollment_id' => $this->enrollment->id,
            'course_id' => $this->enrollment->course_id,
            'course_title' => $this->enrollment->course->title,
            'completed_at' => $this->enrollment->completed_at,
        ];
    }
}