<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        //
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('cloudradar:host-history:cleanup-soft-deleted')->at('00:05');
        $schedule->command('cloudradar:user:cleanup-support')->at('00:35');
        $schedule->command('cloudradar:team:freeze-expired-trials')->at('01:15');
        $schedule->command('cloudradar:team:cleanup-frozen')->at('02:15');
        $schedule->command('cloudradar:general:cleanup')->weekly();
        $schedule->command('cloudradar:user:reminders')->hourly();
        $schedule->command('cloudradar:fetch-and-cache-articles')->hourly();

        $schedule->command('queue:restart')->hourly();
        if ($this->app->environment('staging', 'local')) {
            $schedule->command('telescope:prune')->daily();
        }
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
