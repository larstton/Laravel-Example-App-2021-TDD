<?php

namespace App\Rules;

use App\Models\Host;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;

class ServiceCheckProtocolPortUnique implements Rule
{
    public function passes($attribute, $value)
    {
        $port = request('port');
        $protocol = request('protocol');

        if (empty($port) || empty($protocol)) {
            return true;
        }

        /** @var Host $host */
        $host = request()->route('host');

        return Host::query()
            ->withoutGlobalScopes()
            ->withTrashed()
            ->where('id', $host->id)
            ->whereHas('serviceChecks', function (Builder $query) use ($protocol, $port) {
                $query->where('port', $port)
                    ->where('protocol', $protocol);
            })->doesntExist();
    }

    public function message()
    {
        return 'You already have a service check with this port and protocol.';
    }
}
