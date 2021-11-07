<?php

/** @noinspection PhpUnusedPrivateMethodInspection */

namespace App\Actions\Host;

use App\Enums\TeamPlan;
use App\Models\Host;
use LogicException;

class LogHostHistoryAction
{
    public function execute(Host $host, string $method): void
    {
        throw_unless(in_array($method, [
            'create', 'update', 'delete',
        ]), LogicException::class);

        call_user_func_array([$this, $method], [$host]);
    }

    private function create(Host $host)
    {
        $host->histories()->create([
            'team_id' => $host->team_id,
            'user_id' => $host->user_id,
            'name'    => $host->name,
            'paid'    => false,
        ]);

        if ($host->team->plan->is(TeamPlan::Payg())) {
            $host->histories()->create([
                'team_id' => $host->team_id,
                'user_id' => $host->user_id,
                'name'    => $host->name,
                'paid'    => true,
            ]);
        }
    }

    private function update(Host $host)
    {
        $host->histories()->update([
            'name' => $host->name,
        ]);
    }

    private function delete(Host $host)
    {
        // Soft deletes history.
        $host->histories()->delete();
    }
}
