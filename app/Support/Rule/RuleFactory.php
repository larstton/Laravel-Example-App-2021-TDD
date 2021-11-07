<?php

namespace App\Support\Rule;

use App\Enums\Rule\RuleThresholdUnit;
use App\Models\Concerns\AuthedEntity;
use App\Models\Rule;

class RuleFactory
{
    public static function makeGeneralSuccessAlertRule(AuthedEntity $authedEntity): PendingRule
    {
        return Rule::newRuleForTeam($authedEntity->team)
            ->createdByAuthedEntity($authedEntity)
            ->withWebCheck()
            ->withServiceCheck()
            ->setCheckKey('*.success')
            ->withExpressionAlias('failed_3_times');
    }

    public static function makeDiskFullAlertRule(AuthedEntity $authedEntity): PendingRule
    {
        return Rule::newRuleForTeam($authedEntity->team)
            ->createdByAuthedEntity($authedEntity)
            ->withAgentCheck()
            ->setCheckKey('fs.free_percent.*')
            ->setThreshold(5)
            ->setResultsRange(1)
            ->withLastFunction()
            ->withLessThanOperator();
    }

    /**
     * @param  AuthedEntity  $authedEntity
     * @return PendingRule
     * @see https://tracker.cloudradar.info/issue/DEV-560
     */
    public static function makeLowMemoryWarningRule(AuthedEntity $authedEntity): PendingRule
    {
        return Rule::newRuleForTeam($authedEntity->team)
            ->createdByAuthedEntity($authedEntity)
            ->withAgentCheck()
            ->withWarningAction()
            ->setCheckKey('mem.available_percent')
            ->setThreshold(5)
            ->setResultsRange(3)
            ->withAverageFunction()
            ->withLessThanOperator();
    }

    /**
     * @param  AuthedEntity  $authedEntity
     * @return PendingRule
     * @see https://tracker.cloudradar.info/issue/DEV-560
     */
    public static function makeCPUHighLoadWarningRule(AuthedEntity $authedEntity): PendingRule
    {
        return Rule::newRuleForTeam($authedEntity->team)
            ->createdByAuthedEntity($authedEntity)
            ->withAgentCheck()
            ->withWarningAction()
            ->setCheckKey('cpu.util.idle.*.total')
            ->setThreshold(10)
            ->setResultsRange(3)
            ->withAverageFunction()
            ->withLessThanOperator();
    }

    /**
     * @param  AuthedEntity  $authedEntity
     * @return PendingRule
     * @see https://tracker.cloudradar.info/issue/DEV-1313
     */
    public static function makeModuleWarningRule(AuthedEntity $authedEntity): PendingRule
    {
        return self::makeModuleAlertRule($authedEntity)->withWarningAction();
    }

    /**
     * @param  AuthedEntity  $authedEntity
     * @return PendingRule
     * @see https://tracker.cloudradar.info/issue/DEV-1313
     */
    public static function makeModuleAlertRule(AuthedEntity $authedEntity): PendingRule
    {
        return Rule::newRuleForTeam($authedEntity->team)
            ->createdByAuthedEntity($authedEntity)
            ->withAgentCheck()
            ->setCheckKey('modules')
            ->setThreshold(1)
            ->setResultsRange(0)
            ->withLastFunction()
            ->withNotEmptyOperator();
    }

    /**
     * @param  AuthedEntity  $authedEntity
     * @return PendingRule
     * @see https://tracker.cloudradar.info/issue/DEV-344
     */
    public static function makeHttpPerformanceWarningRule(AuthedEntity $authedEntity): PendingRule
    {
        return Rule::newRuleForTeam($authedEntity->team)
            ->createdByAuthedEntity($authedEntity)
            ->withWebCheck()
            ->withWarningAction()
            ->setCheckKey('http.*.performance_s')
            ->setThreshold(20)
            ->setUnit(RuleThresholdUnit::Second())
            ->setResultsRange(5)
            ->withAverageFunction()
            ->withGreaterThanOperator();
    }

    /**
     * @param  AuthedEntity  $authedEntity
     * @return PendingRule
     * @see https://tracker.cloudradar.info/issue/DEV-344
     */
    public static function makeICMPRoundTripAlertRule(AuthedEntity $authedEntity): PendingRule
    {
        return Rule::newRuleForTeam($authedEntity->team)
            ->createdByAuthedEntity($authedEntity)
            ->withServiceCheck()
            ->withWarningAction()
            ->setCheckKey('net.icmp.ping.round_trip_time_s')
            ->setThreshold(1)
            ->setUnit(RuleThresholdUnit::Second())
            ->setResultsRange(5)
            ->withAverageFunction()
            ->withGreaterThanOperator();
    }

    /**
     * @param  AuthedEntity  $authedEntity
     * @return PendingRule
     * @see https://tracker.cloudradar.info/issue/DEV-344
     */
    public static function makeICMPPacketLossAlertRule(AuthedEntity $authedEntity): PendingRule
    {
        return Rule::newRuleForTeam($authedEntity->team)
            ->createdByAuthedEntity($authedEntity)
            ->withServiceCheck()
            ->setCheckKey('net.icmp.ping.packetLoss_percent')
            ->setThreshold(85)
            ->setResultsRange(5)
            ->withAverageFunction()
            ->withGreaterThanOperator();
    }

    /**
     * @param  AuthedEntity  $authedEntity
     * @return PendingRule
     * @see https://tracker.cloudradar.info/issue/DEV-1218
     */
    public static function makeSmartCustomCheckWarningRule(AuthedEntity $authedEntity): PendingRule
    {
        return self::makeSmartCustomCheckAlertRule($authedEntity)
            ->setCheckKey('*warning')
            ->withWarningAction();
    }

    /**
     * @param  AuthedEntity  $authedEntity
     * @return PendingRule
     * @see https://tracker.cloudradar.info/issue/DEV-1218
     */
    public static function makeSmartCustomCheckAlertRule(AuthedEntity $authedEntity): PendingRule
    {
        return Rule::newRuleForTeam($authedEntity->team)
            ->createdByAuthedEntity($authedEntity)
            ->withCustomCheck()
            ->setCheckKey('*alert')
            ->setThreshold(0)
            ->setResultsRange(1)
            ->withLastFunction()
            ->withNotEmptyOperator();
    }

    /**
     * @param  AuthedEntity  $authedEntity
     * @return PendingRule
     * @see https://tracker.cloudradar.info/issue/DEV-1024
     */
    public static function makeCustomCheckSuccessAlertRule(AuthedEntity $authedEntity): PendingRule
    {
        return Rule::newRuleForTeam($authedEntity->team)
            ->createdByAuthedEntity($authedEntity)
            ->withCustomCheck()
            ->setCheckKey('*.success')
            ->withExpressionAlias('failed_1_times');
    }
}
