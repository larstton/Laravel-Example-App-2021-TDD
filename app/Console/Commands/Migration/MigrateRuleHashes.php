<?php

namespace App\Console\Commands\Migration;

use App\Exceptions\RuleException;
use App\Models\Rule;
use App\Support\Tenancy\Facades\TenantManager;
use Illuminate\Console\Command;

class MigrateRuleHashes extends Command
{
    protected $signature = 'cloudradar:v3-migrate:rule-hashes';

    public function handle()
    {
        set_time_limit(1000);
        TenantManager::disableTenancyChecks();
        activity()->disableLogging();

        $count = Rule::query()->count();

        $this->info($count.' rules to rehash...');
        $this->output->progressStart($count);

        $rulesToDelete = collect();

        // Loop over every rule and recalculate the hash
        Rule::query()->cursor()->each(function (Rule $rule) use ($rulesToDelete) {
            $this->output->progressAdvance();
            try {
                $rule->calculateChecksum();
                $rule->save();
            } catch (RuleException $exception) {
                $rulesToDelete->push($rule);
            }
        });

        $this->output->progressFinish();

        if ($rulesToDelete->isNotEmpty()) {
            $count = $rulesToDelete->count();

            $this->info("There are {$count} rule(s) to delete as they clash. Try update hash again to be sure and then delete if failed...");

            $this->output->progressStart($count);
            $rulesToDelete->each(function (Rule $rule) {
                $this->output->progressAdvance();
                try {
                    $rule->calculateChecksum();
                    $rule->save();
                } catch (RuleException $exception) {
                    $rule->delete();
                }
            });
            $this->output->progressFinish();
        }
    }
}
