<?php

namespace App\Console\Commands\Utility;

use App\Models\Team;
use Illuminate\Console\Command;

class DeconstructRegistrationTrackCommand extends Command
{
    protected $signature = 'cloudradar:registrationtrack:deconstruct';

    protected $description = 'Transform the registration track in to machine readable json';

    public function handle()
    {
        // Team::query()
        //     ->whereNotNull('registration_track')
        //     ->where('registration_track', 'LIKE', '%/?utm%')
        //     ->where('registration_track', 'NOT LIKE', '%\"utm_source\":%')
        //     ->limit(10)
        //     ->cursor()
        //     ->each(function(Team $team) {
        //
        //     });
    }
}
