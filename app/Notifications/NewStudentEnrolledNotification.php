<?php

namespace App\Notifications;

use App\Models\Enrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewStudentEnrolledNotification extends Notification
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
        $student = $this->enrollment->user;
        $course = $this->enrollment->course;

        return (new MailMessage)
            ->subject('New Student Enrolled - ' . $course->title)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Good news! A new student has enrolled in your course.')
            ->line('**Student Information:**')
            ->line('• Name: ' . $student->name)
            ->line('• Email: ' . $student->email)
            ->line('• Enrolled in: ' . $course->title)
            ->line('• Enrollment Date: ' . $this->enrollment->created_at->format('F d, Y'))
            ->line('• Amount Paid: $' . number_format($this->enrollment->price_paid, 2))
            ->action('View Student Profile', route('students.show', $student))
            ->line('Keep creating great content!')
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
            'student_id' => $this->enrollment->user_id,
            'student_name' => $this->enrollment->user->name,
            'course_id' => $this->enrollment->course_id,
            'course_title' => $this->enrollment->course->title,
        ];
    }
}