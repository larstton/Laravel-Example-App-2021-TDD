<?php

namespace Tests\Unit\Actions\HostHistory;

use App\Actions\HostHistory\BuildHostHistoryUsageStatisticsAction;
use App\Models\HostHistory;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

class BuildHostHistoryUsageStatisticsActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function will_correctly_sum_days_from_paid_host_history_for_given_month()
    {
        $team = $this->createTeam();

        // July has 31 days
        $createdAt = now()->setMonth(7)->setYear(2020)->startOfMonth();

        // Getting stats for month of July...
        // Created on 10th day of July means paid period will be 31 days - 10 days = 22 days
        // (10th day inclusive to 31st day inclusive)
        HostHistory::factory()->count(5)->for($team)->create([
            'paid'       => true,
            'created_at' => $createdAt->clone()->setDay(10),
        ]);

        // These will be excluded as they arent for paid hosts.
        HostHistory::factory()->for($team)->create([
            'paid'       => false,
            'created_at' => $createdAt->clone()->setDay(10),
        ]);

        $data = resolve(BuildHostHistoryUsageStatisticsAction::class)->execute(
            $createdAt->clone()
        );

        $this->assertEquals(22 * 5, $data['days']);
        $this->assertEquals(0, $data['months']);
    }

    /** @test */
    public function will_correctly_sum_months_from_paid_host_history_for_given_month()
    {
        $team = $this->createTeam();
        $createdAt = now()->setMonth(7)->setYear(2020)->startOfMonth();

        HostHistory::factory()->count(5)->for($team)->create([
            'paid'       => true,
            'created_at' => $createdAt->clone()->setDay(10),
        ]);

        $data = resolve(BuildHostHistoryUsageStatisticsAction::class)->execute(
            $createdAt->clone()->addMonth()
        );

        $this->assertEquals(0, $data['days']);
        $this->assertEquals(5, $data['months']);
    }

    /** @test */
    public function will_include_soft_deleted_host_history()
    {
        $team = $this->createTeam();
        $createdAt = now()->setMonth(7)->setYear(2020)->startOfMonth();

        HostHistory::factory()->count(5)->for($team)->create([
            'paid'       => true,
            'created_at' => $createdAt->clone()->setDay(10),
            'deleted_at' => $createdAt->clone()->setDay(20),
        ]);

        $data = resolve(BuildHostHistoryUsageStatisticsAction::class)->execute(
            $createdAt->clone()
        );

        $this->assertEquals(55, $data['days']);
        $this->assertEquals(0, $data['months']);
    }
}
