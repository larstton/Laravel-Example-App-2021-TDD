<?php

namespace App\Jobs\Host;

use App\Actions\Host\PostHostDeleteTidyUpAction;
use App\Models\ApiToken;
use App\Models\Concerns\AuthedEntity;
use App\Models\Host;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PostDeleteHostTidyUp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $hostId;

    public bool $purgeReports;

    public function __construct(Host $host, bool $purgeReports = false)
    {
        $this->hostId = $host->id;
        $this->purgeReports = $purgeReports;
    }

    public function handle(PostHostDeleteTidyUpAction $hardDeleteHostAction)
    {
        if (is_null($host = Host::onlyTrashed()->find($this->hostId))) {
            $this->fail();

            return;
        }

        if (is_null($authedEntity = $this->resolveAuthedEntity($host->user_id))) {
            $this->fail();

            return;
        }

        $hardDeleteHostAction->execute($host, $authedEntity, $this->purgeReports);
    }

    private function resolveAuthedEntity($entityId): ?AuthedEntity
    {
        return User::find($entityId) ?? ApiToken::find($entityId);
    }
}
