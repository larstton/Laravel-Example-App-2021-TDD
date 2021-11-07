<?php

namespace App\Actions\ServiceCheck;

use App\Data\ServiceCheck\ServiceCheckData;
use App\Models\Host;
use App\Models\ServiceCheck;
use App\Models\User;
use App\Support\Preflight\Contract\CheckPreflight;
use App\Support\Rule\RuleFactory;
use Illuminate\Support\Facades\DB;

class CreateServiceCheckAction
{
    private CheckPreflight $checkPreflight;

    public function __construct(CheckPreflight $checkPreflight)
    {
        $this->checkPreflight = $checkPreflight;
    }

    public function execute(User $user, Host $host, ServiceCheckData $serviceCheckData): ServiceCheck
    {
        // TODO Id rather this returns an object where we can check the result and get messages etc
        // throwing an exception to control logic feels icky.
        $this->checkPreflight->serviceCheck($host, $serviceCheckData);

        return DB::transaction(function () use ($serviceCheckData, $user, $host): ServiceCheck {
            $serviceCheck = $host->serviceChecks()->create([
                'user_id'        => $user->id,
                'protocol'       => $serviceCheckData->protocol,
                'service'        => $serviceCheckData->service,
                'port'           => $serviceCheckData->port ?? 0,
                'active'         => $serviceCheckData->active,
                'check_interval' => $serviceCheckData->protocol === 'ssl'
                    ? 3600
                    : $serviceCheckData->checkInterval,
            ]);

            if ($serviceCheck->service === 'ping') {
                RuleFactory::makeICMPRoundTripAlertRule($user)->saveIfNew($host);
                RuleFactory::makeICMPPacketLossAlertRule($user)->saveIfNew($host);
            }

            /** @var ServiceCheck $serviceCheck */
            return $serviceCheck;
        });
    }
}
