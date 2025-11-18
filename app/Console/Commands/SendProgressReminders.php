<?php

namespace App\Console\Commands;

use App\Models\Enrollment;
use Illuminate\Console\Command;
use App\Notifications\CourseProgressReminderNotification;

class SendProgressReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-progress-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
         $inactiveEnrollments = Enrollment::where('progress', '>', 0)
            ->where('progress', '<', 100)
            ->where('updated_at', '<', now()->subDays(7))
            ->get();

        foreach ($inactiveEnrollments as $enrollment) {
            $enrollment->user->notify(new CourseProgressReminderNotification($enrollment));
        }

        $this->info('Sent ' . $inactiveEnrollments->count() . ' progress reminders.');
    }
}

