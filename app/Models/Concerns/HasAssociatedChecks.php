<?php

namespace App\Models\Concerns;

use App\Models\Host;
use Illuminate\Database\Eloquent\Builder;

trait HasAssociatedChecks
{
    public function scopeWhereCheckIdMatchesChecksOfHost(Builder $query, Host $host)
    {
        $query->where(function ($query) use ($host) {
            $query
                ->whereIn('check_id', function ($query) use ($host) {
                    $query
                        ->select('id')
                        ->from('service_checks')
                        ->where('host_id', $host->id);
                })
                ->orWhereIn('check_id', function ($query) use ($host) {
                    $query
                        ->select('id')
                        ->from('web_checks')
                        ->where('host_id', $host->id);
                })
                ->orWhereIn('check_id', function ($query) use ($host) {
                    $query
                        ->select('id')
                        ->from('snmp_checks')
                        ->where('host_id', $host->id);
                })
                ->orWhereIn('check_id', function ($query) use ($host) {
                    $query
                        ->select('id')
                        ->from('custom_checks')
                        ->where('host_id', $host->id);
                })
                ->orWhere('check_id', $host->id);
        });
    }
}
