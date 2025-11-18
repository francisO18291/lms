<?php

namespace App\Notifications;

use App\Models\Enrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EnrollmentConfirmation extends Notification
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
        $firstSection = $course->sections()->orderBy('order')->first();
        $firstLesson = $firstSection ? $firstSection->lessons()->orderBy('order')->first() : null;

        $mailMessage = (new MailMessage)
            ->subject('Enrollment Confirmation - ' . $course->title)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('You have successfully enrolled in **' . $course->title . '**.')
            ->line('**Course Details:**')
            ->line('• Instructor: ' . $course->teacher->name)
            ->line('• Level: ' . ucfirst($course->level))
            ->line('• Duration: ' . ($course->duration_hours ? $course->duration_hours . ' hours' : 'Self-paced'))
            ->line('• Price Paid: $' . number_format($this->enrollment->price_paid, 2))
            ->line('You can now start learning at your own pace.');

        // Only add the action button if we have a first lesson
        if ($firstLesson) {
            $mailMessage->action('Start Learning', route('courses.lessons.show', [$course, $firstLesson]));
        } else {
            $mailMessage->action('View Course', route('courses.show', $course));
        }

        return $mailMessage
            ->line('Good luck with your learning journey!')
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
            'enrollment_id' => $this->enrollment->id,
            'course_id' => $this->enrollment->course_id,
            'course_title' => $this->enrollment->course->title,
        ];
    }
}