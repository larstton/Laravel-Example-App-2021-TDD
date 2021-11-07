<?php

namespace App\Actions\CustomCheck;

use App\Data\CustomCheck\CustomCheckData;
use App\Models\CustomCheck;
use App\Models\Host;
use App\Models\User;
use App\Support\Rule\RuleFactory;
use Illuminate\Support\Facades\DB;

class CreateCustomCheckAction
{
    public function execute(User $user, Host $host, CustomCheckData $customCheckData): CustomCheck
    {
        return DB::transaction(function () use ($customCheckData, $user, $host) {
            $customCheck = $host->customChecks()->create([
                'user_id'                  => $user->id,
                'name'                     => $customCheckData->name,
                'expected_update_interval' => $customCheckData->expectedUpdateInterval,
                'token'                    => CustomCheck::makeUniqueToken(),
            ]);

            RuleFactory::makeSmartCustomCheckAlertRule($user)->saveIfNew();
            RuleFactory::makeSmartCustomCheckWarningRule($user)->saveIfNew();
            if (RuleFactory::makeCustomCheckSuccessAlertRule($user)->saveIfNew()) {
                team_settings($user->team)->set([
                    'heartbeats.custom.active' => true,
                ]);
            }

            return $customCheck;
        });
    }
}
