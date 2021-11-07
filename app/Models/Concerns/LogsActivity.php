<?php

namespace App\Models\Concerns;

use App\Models\ActivityLog;
use App\Models\Host;
use App\Support\Tenancy\Facades\TenantManager;
use Spatie\Activitylog\Traits\LogsActivity as BaseLogsActivity;

trait LogsActivity
{
    use BaseLogsActivity {
        BaseLogsActivity::shouldLogEvent as _shouldLogEvent;
    }

    protected static $logAttributes = ['*'];

    public function getDescriptionForEvent(string $eventName): string
    {
        $description = $this->setActivityLogAction($eventName);

        if (! is_null($this->host_id)) {
            $host = Host::withTrashed()->find($this->host_id);
            if (! is_null($host)) {
                $description = sprintf('%s for Host %s (%s)', $description, $host->name, $host->connect);
            } else {
                $description = sprintf('%s for Host (%s)', $description, $this->host_id);
            }
        }

        return $description;
    }

    abstract protected function setActivityLogAction(string $eventName): string;

    public function tapActivity(ActivityLog $activity, string $eventName)
    {
        $activity->team_id = optional(TenantManager::getCurrentTenant())->id;
    }

    protected function shouldLogEvent(string $eventName): bool
    {
        if (! $shouldLog = $this->_shouldLogEvent($eventName)) {
            return false;
        }

        if (method_exists($this, 'shouldLogActivity')) {
            return $this->shouldLogActivity($eventName);
        }

        return true;
    }

    protected function appendActive()
    {
        if (is_null($this->active) || $this->active === false) {
            return 'active=0';
        }

        return 'active='.(string) $this->active;
    }

    protected function appendCheckInterval()
    {
        return sprintf('checkInterval=%s ', $this->check_interval);
    }
}
