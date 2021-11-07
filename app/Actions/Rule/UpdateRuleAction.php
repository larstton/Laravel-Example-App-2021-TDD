<?php

namespace App\Actions\Rule;

use App\Data\Rule\RuleData;
use App\Models\Event;
use App\Models\Rule;
use Facades\App\Support\NotifierService;

class UpdateRuleAction
{
    public function execute(Rule $rule, RuleData $ruleData): Rule
    {
        $pendingRule = $rule->updateExistingRule()
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

        if (! $pendingRule->updateWithoutClash()) {
            fail_validation([
                'rule' => 'This rule already exists for your team.',
            ]);
        }

        $rule->refresh();

        if ($rule->wasChanged('action')) {
            Event::whereRuleId($rule->id)
                ->whereActive()
                ->get()
                ->each->update([
                    'action' => $rule->action->value,
                ]);
        }

        $changedAttributes = collect($rule->getAttributes())->except([
            'id', 'team_id', 'user_id', 'active', 'finish', 'mandatory', 'position',
            'action', 'threshold', 'unit', 'checksum', 'created_at', 'updated_at'
        ])->keys()->toArray();

        if ($rule->wasChanged($changedAttributes)) {
            Event::whereRuleId($rule->id)
                ->whereActive()
                ->each(function (Event $event) {
                    NotifierService::recoverEvent($event);
                    $event->delete();
                });
        }

        return $rule;
    }
}
