<?php

namespace App\Casts;

use App\Enums\CheckLastSuccess;
use App\Models\CustomCheck;
use App\Models\ServiceCheck;
use App\Models\SnmpCheck;
use App\Models\WebCheck;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class CheckLastSuccessStatusCast implements CastsAttributes
{
    /**
     * Check intervals will be multiplied by this number to allow for hub delays etc.
     */
    const TOLERANCE = 1.1;

    /**
     * @param  WebCheck|SnmpCheck|ServiceCheck|CustomCheck  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return CheckLastSuccess
     */
    public function get($model, string $key, $value, array $attributes): CheckLastSuccess
    {
        if (is_a($model, CustomCheck::class)) {
            return $this->makeCustomCheckLastSuccess($model);
        }

        $lastSuccess = CheckLastSuccess::coerce($value);

        if ($lastSuccess->is(CheckLastSuccess::Pending())) {
            return $lastSuccess;
        }

        if (now()->subSeconds($model->check_interval * self::TOLERANCE)
            ->startOfMinute()
            ->isAfter($model->last_checked_at->endOfMinute())
        ) {
            return CheckLastSuccess::NoData();
        }

        if ($lastSuccess->is(CheckLastSuccess::Failed())) {
            return $lastSuccess;
        }

        return CheckLastSuccess::Success();
    }

    private function makeCustomCheckLastSuccess(CustomCheck $customCheck)
    {
        $expectedLastExecution = now()->subSeconds(
            $customCheck->expected_update_interval * self::TOLERANCE
        );

        // If custom check has been updated since the last time the check executed, and the update
        // occurred within the check interval in seconds, then show as pending.
        if (
            optional($customCheck->updated_at)->isAfter($customCheck->last_checked_at)
            && optional($customCheck->updated_at)->isAfter($expectedLastExecution)
        ) {
            return CheckLastSuccess::Pending();
        }

        if ($expectedLastExecution->isAfter($customCheck->last_checked_at)) {
            return CheckLastSuccess::NoData();
        }

        return CheckLastSuccess::Success();
    }

    public function set($model, $key, $value, $attributes)
    {
        return $value;
    }
}
