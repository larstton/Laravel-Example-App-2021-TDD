<?php

namespace App\Support\Rule;

use App\Enums\Rule\RuleAction;
use App\Enums\Rule\RuleFunction;
use App\Enums\Rule\RuleOperator;
use App\Models\Rule;
use Illuminate\Support\Str;

abstract class RuleNameBuilder
{
    protected Rule $rule;
    protected $uuid;
    protected $argument;
    protected RuleFunction $function;
    protected RuleOperator $operator;
    protected $resultsRange;
    protected $threshold;
    protected RuleAction $action;

    public function __construct(Rule $rule)
    {
        $this->rule = $rule;
        $this->argument = $rule->check_key;
        $this->function = $rule->function;
        $this->operator = $rule->operator;
        $this->resultsRange = $rule->results_range;
        $this->threshold = $rule->threshold;
        $this->uuid = $rule->id;
        $this->action = $rule->action;
    }

    public function build(): string
    {
        $lookup = [
            'successSum'                => fn () => $this->makeSuccessSumRuleName(),
            'icmpAvgPacketLoss'         => fn () => $this->makeIcmpAvgPacketLossRuleName(),
            'icmpAvgRoundTripTime'      => fn () => $this->makeIcmpAvgRoundTripTimeRuleName(),
            'fileSystemLastFreePercent' => fn () => $this->makeFileSystemLastFreePercentRuleName(),
            'cpuUtil'                   => fn () => $this->makeCpuUtilRuleName(),
            'memory'                    => fn () => $this->makeMemoryRuleName(),
            'agentHeartbeat'            => fn () => $this->makeAgentHeartbeatRuleName(),
            'frontmanHeartbeat'         => fn () => $this->makeFrontmanHeartbeatRuleName(),
            'keyFunctionProcList'       => fn () => $this->makeKeyFunctionProcListRuleName(),
            'module'                    => fn () => $this->makeModuleRuleName(),
        ];

        switch (true) {
            case $this->isSuccessSumRule():
                return $lookup['successSum']();
            case $this->isIcmpPacketLossPercentWithAvgFunctionRule():
                return $lookup['icmpAvgPacketLoss']();
            case $this->isIcmpPingRoundTripTimeInSecondsWithAvgFunctionRule():
                return $lookup['icmpAvgRoundTripTime']();
            case $this->isFilesSystemFreePercentWithLastFunctionRule():
                return $lookup['fileSystemLastFreePercent']();
            case $this->isCpuUtilRule():
                return $lookup['cpuUtil']();
            case $this->isMemoryRule():
                return $lookup['memory']();
            case $this->isAgentHeartbeatRule():
                return $lookup['agentHeartbeat']();
            case $this->isFrontmanHeartbeatRule():
                return $lookup['frontmanHeartbeat']();
            case $this->isKeyFunctionProcListRule():
                return $lookup['keyFunctionProcList']();
            case $this->isModuleRule():
                return $lookup['module']();
            default:
                $str = ':check-key.:function(:results-range):operator:threshold';
                $params = [
                    'check-key'     => $this->getCheckKey(),
                    'function'      => $this->getFunction(),
                    'results-range' => $this->getResultsRange(),
                    'operator'      => $this->getOperator(),
                    'threshold'     => $this->getThreshold(),
                ];

                return $this->replaceAndReturnName($str, $params);
        }
    }

    private function makeSuccessSumRuleName(): string
    {
        $parts = $this->getCheckKeyParts();
        $checkKey = Str::replaceFirst('.success', '', $this->getCheckKey());
        $protocol = $parts[2] ?? null;
        $port = $parts[3] ?? null;

        if (Str::contains($checkKey, 'net.tcp.tcp')) {
            $str = 'TCP Port :port Check';
        } elseif (Str::contains($checkKey, 'net.tcp')) {
            $str = ':protocol Check (Port :port)';
        } else {
            $str = (string) Str::of($checkKey)->replace('.', ' ')->title();
        }

        $str = $str.' has failed :results-range :time';
        $params = [
            'protocol'      => $protocol,
            'port'          => $port,
            'results-range' => $this->getResultsRange(),
            'time'          => Str::plural('time', $this->getResultsRange()),
        ];

        return $this->replaceAndReturnName($str, $params);
    }

    private function getCheckKeyParts(): array
    {
        return explode('.', $this->getCheckKey());
    }

    public function getCheckKey()
    {
        return $this->getArgument();
    }

    public function getArgument()
    {
        return $this->argument;
    }

    public function getResultsRange()
    {
        return $this->resultsRange;
    }

    private function replaceAndReturnName($str, $params): string
    {
        return __($str, $params);
    }

    private function makeIcmpAvgPacketLossRuleName(): string
    {
        $str = 'Average of last :results-range :measurement of icmp.ping.packetLoss :operator :threshold%';
        $params = [
            'results-range' => $this->getResultsRange(),
            'measurement'   => Str::plural('measurement', $this->getResultsRange()),
            'operator'      => $this->getOperator(),
            'threshold'     => round($this->getThreshold(), 2),
        ];

        return $this->replaceAndReturnName($str, $params);
    }

    public function getOperator(): RuleOperator
    {
        return $this->operator;
    }

    public function getThreshold()
    {
        return $this->threshold;
    }

    private function makeIcmpAvgRoundTripTimeRuleName(): string
    {
        $str = 'Average of last :results-range :measurement of icmp.ping.round_trip_time_s :operator :threshold second(s)';
        $params = [
            'results-range' => $this->getResultsRange(),
            'measurement'   => Str::plural('measurement', $this->getResultsRange()),
            'operator'      => $this->getOperator(),
            'threshold'     => round($this->getThreshold(), 2),
        ];

        return $this->replaceAndReturnName($str, $params);
    }

    private function makeFileSystemLastFreePercentRuleName(): string
    {
        $parts = $this->getCheckKeyParts();
        $str = 'Filesystem :filesystem free :operator :threshold%';
        $params = [
            'filesystem' => $parts[2],
            'operator'   => $this->getOperator(),
            'threshold'  => round($this->getThreshold(), 2),
        ];

        return $this->replaceAndReturnName($str, $params);
    }

    private function makeCpuUtilRuleName(): string
    {
        $parts = $this->getCheckKeyParts();

        if ($this->getFunction()->is(RuleFunction::Last())) {
            $str = 'Last measurement of CPU Utilization :cpu-util-type (Total*) :operator :threshold%';
        } else {
            $str = ':function of :results-range last :measurement of CPU Utilization :cpu-util-type (Total*) :operator :threshold%';
        }

        $params = [
            'function'      => Str::ucfirst($this->getFunction()),
            'cpu-util-type' => $parts[2],
            'results-range' => $this->getResultsRange(),
            'measurement'   => Str::plural('measurement', $this->getResultsRange()),
            'operator'      => $this->getOperator(),
            'threshold'     => round($this->getThreshold(), 2),
        ];

        return $this->replaceAndReturnName($str, $params);
    }

    public function getFunction(): RuleFunction
    {
        return $this->function;
    }

    private function makeMemoryRuleName(): string
    {
        $parts = $this->getCheckKeyParts();

        if ($this->getFunction()->is(RuleFunction::Last())) {
            $str = 'Last measurement of memory :memory-type :operator :threshold%';
        } else {
            $str = ':function of :results-range last :measurement of memory :memory-type :operator :threshold%';
        }

        $params = [
            'function'      => Str::ucfirst($this->getFunction()),
            'memory-type'   => (string) Str::of($parts[1])->replace('_', ' ')->title(),
            'results-range' => $this->getResultsRange(),
            'measurement'   => Str::plural('measurement', $this->getResultsRange()),
            'operator'      => $this->getOperator(),
            'threshold'     => round($this->getThreshold(), 2),
        ];

        return $this->replaceAndReturnName($str, $params);
    }

    private function makeAgentHeartbeatRuleName(): string
    {
        $str = 'Cagent did not send data for last :threshold :second';
        $params = [
            'second'    => Str::plural('second', $this->getThreshold()),
            'threshold' => $this->getThreshold(),
        ];

        return $this->replaceAndReturnName($str, $params);
    }

    private function makeFrontmanHeartbeatRuleName(): string
    {
        $str = 'Frontman did not send data for last :threshold :second';
        $params = [
            'second'    => Str::plural('second', $this->getThreshold()),
            'threshold' => $this->getThreshold(),
        ];

        return $this->replaceAndReturnName($str, $params);
    }

    private function makeKeyFunctionProcListRuleName(): string
    {
        $str = 'Number of running :key-arg processes :operator :threshold';
        $params = [
            'key-arg'   => $this->getKeyArgument(),
            'operator'  => $this->getOperator(),
            'threshold' => round($this->getThreshold()),
        ];

        return $this->replaceAndReturnName($str, $params);
    }

    private function makeModuleRuleName(): string
    {
        return 'Last module value is not empty';
    }

    private function isSuccessSumRule(): bool
    {
        return Str::contains($this->getCheckKey(), '.success')
            && $this->getFunction()->is(RuleFunction::Sum())
            && (int) $this->getThreshold() === 0;
    }

    private function isIcmpPacketLossPercentWithAvgFunctionRule(): bool
    {
        return $this->getCheckKey() === 'net.icmp.ping.packetLoss_percent'
            && $this->getFunction()->is(RuleFunction::Average());
    }

    private function isIcmpPingRoundTripTimeInSecondsWithAvgFunctionRule(): bool
    {
        return $this->getCheckKey() === 'net.icmp.ping.round_trip_time_s'
            && $this->getFunction()->is(RuleFunction::Average());
    }

    private function isFilesSystemFreePercentWithLastFunctionRule(): bool
    {
        return Str::contains($this->getCheckKey(), 'fs.free_percent')
            && $this->getFunction()->is(RuleFunction::Last());
    }

    private function isCpuUtilRule(): bool
    {
        return Str::contains($this->getCheckKey(), 'cpu.util');
    }

    private function isMemoryRule(): bool
    {
        return Str::contains($this->getCheckKey(), 'mem.');
    }

    private function isAgentHeartbeatRule(): bool
    {
        return $this->getCheckKey() === 'cagent.heartbeat';
    }

    private function isFrontmanHeartbeatRule(): bool
    {
        return $this->getCheckKey() === 'frontman.heartbeat';
    }

    private function isKeyFunctionProcListRule(): bool
    {
        return $this instanceof JsonFunctionRuleNameBuilder && $this->getListKey() === 'proc.list';
    }

    private function isModuleRule(): bool
    {
        return Str::contains($this->getCheckKey(), 'modules');
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    public function getAction(): RuleAction
    {
        return $this->action;
    }

    public function getLastValue(object $measurements)
    {
        return $measurements->{$this->getCheckKey()};
    }

    public function getProblemKey()
    {
        return $this->getCheckKey();
    }
}
