<?php

namespace App\Notifications;

use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CoursePublishedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Course $course
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
        return (new MailMessage)
            ->subject('Your Course Has Been Published!')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Great news! Your course **' . $this->course->title . '** has been successfully published.')
            ->line('Students can now discover and enroll in your course.')
            ->line('**Course Details:**')
            ->line('• Title: ' . $this->course->title)
            ->line('• Category: ' . $this->course->category->name)
            ->line('• Level: ' . ucfirst($this->course->level))
            ->line('• Price: $' . number_format($this->course->price, 2))
            ->action('View Course', route('courses.show', $this->course))
            ->line('**Tips for Success:**')
            ->line('• Share your course on social media')
            ->line('• Engage with students in the comments')
            ->line('• Update content regularly to keep it fresh')
            ->line('• Monitor student feedback and improve')
            ->line('Good luck with your course!')
            ->salutation('Best regards, ' . config('app.name') . ' Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'course_id' => $this->course->id,
            'course_title' => $this->course->title,
            'published_at' => now(),
        ];
    }
}