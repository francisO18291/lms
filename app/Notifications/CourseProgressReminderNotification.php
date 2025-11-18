<?php

namespace App\Notifications;

use App\Models\Enrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CourseProgressReminderNotification extends Notification
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
        $daysInactive = now()->diffInDays($this->enrollment->updated_at);

        return (new MailMessage)
            ->subject('Continue Your Learning Journey!')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('We noticed you haven\'t made progress on **' . $course->title . '** in the last ' . $daysInactive . ' days.')
            ->line('Your current progress: **' . $this->enrollment->progress . '%**')
            ->line('Don\'t let your momentum fade! Just a few minutes a day can make a big difference.')
            ->line('**Why continue?**')
            ->line('• You\'re ' . (100 - $this->enrollment->progress) . '% away from completing this course')
            ->line('• Earn your certificate of completion')
            ->line('• Gain valuable skills for your career')
            ->action('Continue Learning', route('courses.lessons.show', [$course, $course->sections->first()->lessons->first()]))
            ->line('We believe in you! Keep going!')
            ->salutation('You\'ve got this, ' . config('app.name') . ' Team');
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
            'progress' => $this->enrollment->progress,
        ];
    }
}