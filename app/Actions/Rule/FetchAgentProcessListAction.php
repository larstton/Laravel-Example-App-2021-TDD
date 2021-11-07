<?php

namespace App\Actions\Rule;

use App\Models\CheckResult;
use App\Models\Team;
use Illuminate\Support\Facades\Cache;

class FetchAgentProcessListAction
{
    /**
     * @param  Team  $team
     * @param  string  $type "process" || "cmdline"
     * @return mixed
     */
    public function execute(Team $team, string $type)
    {
        return Cache::remember("agent-process-list:{$team->id}:{$type}",
            now()->addMinutes(10),
            function () use ($type, $team) {
                return $team
                    ->agentCheckResults()
                    ->whereJsonLength('data->measurements->proc.list', '>', 0)
                    ->whereHas('host')
                    ->get()
                    ->flatMap(function (CheckResult $checkResult) use ($type) {
                        return collect($checkResult->data['measurements']['proc.list'])
                            ->map(fn ($measure) => $measure[$type === 'process' ? 'name' : 'cmdline']);
                    })
                    ->unique()
                    ->filter()
                    ->values()
                    ->all();
            });
    }
}
