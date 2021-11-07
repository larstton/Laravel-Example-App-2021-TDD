<?php

namespace App\Support\Tenancy\Actions;

use App\Support\Tenancy\Contracts\IsTenant;
use App\Support\Tenancy\Contracts\NotTenantAware;
use App\Support\Tenancy\Facades\TenantManager;
use Illuminate\Queue\Events\JobProcessing;

class MakeQueueTenantAwareAction
{
    const TENANT_ID = 'tenantId';
    const TENANT_CLASS = 'tenantClass';
    const AUTHED_ENTITY_ID = 'authedEntityId';
    const AUTHED_ENTITY_CLASS = 'authedEntityClass';

    public function execute()
    {
        $this
            ->listenForJobsBeingQueued()
            ->listenForJobsBeingProcessed();
    }

    protected function listenForJobsBeingProcessed(): self
    {
        app('events')->listen(JobProcessing::class, function (JobProcessing $event) {
            $tenantId = $event->job->payload()[self::TENANT_ID] ?? null;
            $tenantClass = $event->job->payload()[self::TENANT_CLASS] ?? null;
            $authedEntityId = $event->job->payload()[self::AUTHED_ENTITY_ID] ?? null;
            $authedEntityClass = $event->job->payload()[self::AUTHED_ENTITY_CLASS] ?? null;

            if (! $tenantClass || ! $tenantId) {
                return;
            }

            /** @var IsTenant $tenant */
            if (! $tenant = $tenantClass::find($tenantId)) {
                return;
            }

            $tenant->makeCurrentTenant();

            if ($authedEntityId && $authedEntityClass && $authedEntity = $authedEntityClass::find($authedEntityId)) {
                auth()->setUser($authedEntity);
            }
        });

        return $this;
    }

    protected function listenForJobsBeingQueued(): self
    {
        app('queue')->createPayloadUsing(function ($connectionName, $queue, $payload) {
            $job = $payload['data']['command'];

            if (! $this->isTenantAware($job)) {
                return [];
            }

            return TenantManager::getCurrentTenant()
                ? [
                    self::TENANT_ID           => TenantManager::getCurrentTenant()->id,
                    self::TENANT_CLASS        => get_class(TenantManager::getCurrentTenant()),
                    self::AUTHED_ENTITY_ID    => optional(auth()->user())->id,
                    self::AUTHED_ENTITY_CLASS => auth()->user() ? get_class(auth()->user()) : null,
                ] : [];
        });

        return $this;
    }

    protected function isTenantAware(object $job): bool
    {
        if ($job instanceof NotTenantAware) {
            return false;
        }

        return true;
    }
}
