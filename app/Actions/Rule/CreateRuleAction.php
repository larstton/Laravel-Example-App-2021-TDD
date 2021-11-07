<?php

namespace App\Actions\Rule;

use App\Data\Rule\RuleData;
use App\Models\Rule;
use App\Models\User;

class CreateRuleAction
{
    public function execute(User $user, RuleData $ruleData): Rule
    {
        $pendingRule = Rule::newRuleForTeam($user->team)
            ->createdByAuthedEntity($user)
            ->withCheckTypes($ruleData->checkType)
            ->setResultsRange($ruleData->resultsRange)
            ->setFunction($ruleData->function)
            ->setOperator($ruleData->operator)
            ->setThreshold($ruleData->threshold)
            ->setUnit($ruleData->unit)
            ->setHostMatchCriteria($ruleData->hostMatchCriteria)
            ->setHostMatchPart($ruleData->hostMatchPart)
            ->setCheckKey($ruleData->checkKey)
            ->setKeyFunction($ruleData->keyFunction)
            ->setAction($ruleData->action)
            ->setActiveState($ruleData->active)
            ->setFinishState($ruleData->finish)
            ->withExpressionAlias($ruleData->expressionAlias);

        if (! $rule = $pendingRule->saveIfNew()) {
            fail_validation([
                'rule' => 'This rule already exists for your team.',
            ]);
        }

        return $rule;
    }
}
