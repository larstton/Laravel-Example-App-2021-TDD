<?php

namespace Tests\Unit\Actions\Host;

use App\Actions\Host\UpdateCheckIntervalsForHostChecksAction;
use App\Models\ServiceCheck;
use App\Models\SnmpCheck;
use App\Models\WebCheck;
use Tests\TestCase;

class UpdateCheckIntervalsForHostChecksActionTest extends TestCase
{
    /** @test */
    public function will_update_check_interval_for_webchecks_of_host()
    {
        $team = $this->createTeam();
        $host = $this->createHost([
            'team_id' => $team->id,
        ]);
        WebCheck::factory()
            ->for($host)
            ->create([
                'check_interval' => 66,
            ]);

        $team->min_check_interval = 100;

        resolve(UpdateCheckIntervalsForHostChecksAction::class)->execute($team);

        $this->assertDatabaseHas('web_checks', [
            'host_id'        => $host->id,
            'check_interval' => 100,
        ]);
    }

    /** @test */
    public function will_update_check_interval_for_service_checks_of_host()
    {
        $team = $this->createTeam();
        $host = $this->createHost([
            'team_id' => $team->id,
        ]);
        ServiceCheck::factory()
            ->for($host)
            ->create([
                'check_interval' => 66,
            ]);

        $team->min_check_interval = 100;

        resolve(UpdateCheckIntervalsForHostChecksAction::class)->execute($team);

        $this->assertDatabaseHas('service_checks', [
            'host_id'        => $host->id,
            'check_interval' => 100,
        ]);
    }

    /** @test */
    public function will_update_check_interval_for_snmp_checks_of_host()
    {
        $team = $this->createTeam();
        $host = $this->createHost([
            'team_id' => $team->id,
        ]);
        SnmpCheck::factory()
            ->for($host)
            ->create([
                'check_interval' => 66,
            ]);

        $team->min_check_interval = 100;

        resolve(UpdateCheckIntervalsForHostChecksAction::class)->execute($team);

        $this->assertDatabaseHas('snmp_checks', [
            'host_id'        => $host->id,
            'check_interval' => 100,
        ]);
    }
}
