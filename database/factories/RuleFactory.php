<?php

namespace Database\Factories;

use App\Enums\Rule\RuleAction;
use App\Enums\Rule\RuleCheckType;
use App\Enums\Rule\RuleFunction;
use App\Enums\Rule\RuleHostMatchPart;
use App\Enums\Rule\RuleOperator;
use App\Enums\Rule\RuleThresholdUnit;
use App\Models\Rule;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RuleFactory extends Factory
{
    protected $model = Rule::class;

    public function definition()
    {
        return [
            'id'                  => Str::uuid()->toString(),
            'team_id'             => Team::factory(),
            'host_match_part'     => RuleHostMatchPart::None(),
            'host_match_criteria' => 'any',
            'finish'              => false,
            'action'              => RuleAction::Alert(),
            'position'            => 1,
            'check_key'           => '*.success',
            'check_type'          => [RuleCheckType::ServiceCheck],
            'function'            => RuleFunction::Average(),
            'operator'            => RuleOperator::LessThan(),
            'key_function'        => ['key' => '', 'value' => ''],
            'results_range'       => 5,
            'threshold'           => 10.0,
            'unit'                => null,
            'user_id'             => User::factory(),
            'active'              => true,
            'expression_alias'    => null,
            'checksum'            => md5($this->faker->sentence),
            'mandatory'           => false,
            'created_at'          => now(),
            'updated_at'          => now(),
        ];
    }

    public function makeDiskFullAlertRule()
    {
        return $this->state(function (array $attributes) {
            return [
                'check_type'    => [RuleCheckType::Cagent],
                'check_key'     => 'fs.free_percent.*',
                'threshold'     => 5,
                'results_range' => 1,
                'function'      => RuleFunction::Last(),
                'operator'      => RuleOperator::LessThan(),
                'checksum'      => null,
            ];
        });
    }

    public function makeLowMemoryWarningRule()
    {
        return $this->state(function (array $attributes) {
            return [
                'check_type'    => [RuleCheckType::Cagent],
                'action'        => RuleAction::Warn(),
                'check_key'     => 'mem.available_percent',
                'threshold'     => 5,
                'results_range' => 3,
                'function'      => RuleFunction::Average(),
                'operator'      => RuleOperator::LessThan(),
                'checksum'      => null,
            ];
        });
    }

    public function makeCPUHighLoadWarningRule()
    {
        return $this->state(function (array $attributes) {
            return [
                'check_type'    => [RuleCheckType::Cagent],
                'action'        => RuleAction::Warn(),
                'check_key'     => 'cpu.util.idle.*.total',
                'threshold'     => 10,
                'results_range' => 3,
                'function'      => RuleFunction::Average(),
                'operator'      => RuleOperator::LessThan(),
                'checksum'      => null,
            ];
        });
    }

    public function makeModuleAlertRule()
    {
        return $this->state(function (array $attributes) {
            return [
                'check_type'    => [RuleCheckType::Cagent],
                'action'        => RuleAction::Alert(),
                'check_key'     => 'modules',
                'threshold'     => 1,
                'results_range' => 0,
                'function'      => RuleFunction::Last(),
                'operator'      => RuleOperator::NotEmpty(),
                'checksum'      => null,
            ];
        });
    }

    public function makeModuleWarningRule()
    {
        return $this->state(function (array $attributes) {
            return [
                'check_type'    => [RuleCheckType::Cagent],
                'action'        => RuleAction::Warn(),
                'check_key'     => 'modules',
                'threshold'     => 1,
                'results_range' => 0,
                'function'      => RuleFunction::Last(),
                'operator'      => RuleOperator::NotEmpty(),
                'checksum'      => null,
            ];
        });
    }

    public function makeSmartCustomCheckAlertRule()
    {
        return $this->state(function (array $attributes) {
            return [
                'check_type'    => [RuleCheckType::CustomCheck],
                'check_key'     => '*alert',
                'threshold'     => 0,
                'results_range' => 1,
                'function'      => RuleFunction::Last(),
                'operator'      => RuleOperator::NotEmpty(),
                'checksum'      => null,
            ];
        });
    }

    public function makeSmartCustomCheckWarningRule()
    {
        return $this->state(function (array $attributes) {
            return [
                'check_type'    => [RuleCheckType::CustomCheck],
                'action'        => RuleAction::Warn(),
                'check_key'     => '*warning',
                'threshold'     => 0,
                'results_range' => 1,
                'function'      => RuleFunction::Last(),
                'operator'      => RuleOperator::NotEmpty(),
                'checksum'      => null,
            ];
        });
    }

    public function makeCustomCheckSuccessAlertRule()
    {
        return $this->state(function (array $attributes) {
            return [
                'check_type'       => [RuleCheckType::CustomCheck],
                'check_key'        => '*.success',
                'expression_alias' => 'failed_1_times',
                'results_range'    => '1',
                'function'         => RuleFunction::Sum(),
                'operator'         => RuleOperator::EqualTo(),
                'threshold'        => 0,
                'checksum'         => null,
            ];
        });
    }

    public function makeICMPRoundTripAlertRule()
    {
        return $this->state(function (array $attributes) {
            return [
                'check_type'       => [RuleCheckType::ServiceCheck],
                'action'           => RuleAction::Warn(),
                'check_key'        => 'net.icmp.ping.round_trip_time_s',
                'expression_alias' => null,
                'results_range'    => '5',
                'function'         => RuleFunction::Average(),
                'operator'         => RuleOperator::GreaterThan(),
                'unit'             => RuleThresholdUnit::Second(),
                'threshold'        => 1,
                'checksum'         => null,
            ];
        });
    }

    public function makeICMPPacketLossAlertRule()
    {
        return $this->state(function (array $attributes) {
            return [
                'check_type'       => [RuleCheckType::ServiceCheck],
                'check_key'        => 'net.icmp.ping.packetLoss_percent',
                'expression_alias' => null,
                'results_range'    => '5',
                'function'         => RuleFunction::Average(),
                'operator'         => RuleOperator::GreaterThan(),
                'threshold'        => 85,
                'checksum'         => null,
            ];
        });
    }

    public function makeHttpPerformanceWarningRule()
    {
        return $this->state(function (array $attributes) {
            return [
                'check_type'       => [RuleCheckType::WebCheck],
                'check_key'        => 'http.*.performance_s',
                'expression_alias' => null,
                'results_range'    => '5',
                'action'           => RuleAction::Warn(),
                'function'         => RuleFunction::Average(),
                'operator'         => RuleOperator::GreaterThan(),
                'threshold'        => 20,
                'unit'             => RuleThresholdUnit::Second(),
                'checksum'         => null,
            ];
        });
    }
}
