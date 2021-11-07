<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Recipient;
use App\Models\Reminder;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReminderFactory extends Factory
{
    protected $model = Reminder::class;

    public function definition()
    {
        return [
            'recipient_id'             => Recipient::factory(),
            'event_id'                 => Event::factory(),
            'reminders_count'          => 0,
            'last_reminder_created_at' => now(),
        ];
    }
}
