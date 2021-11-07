<?php

namespace App\Http\Controllers\Rule;

use App\Actions\Rule\CreateRuleAction;
use App\Actions\Rule\DeleteRuleAction;
use App\Actions\Rule\UpdateRuleAction;
use App\Data\Rule\RuleData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Rule\RuleRequest;
use App\Http\Resources\RuleResource;
use App\Models\Rule;

class RuleController extends Controller
{
    public function index()
    {
        return RuleResource::collection(Rule::ordered()->get());
    }

    public function store(RuleRequest $request, CreateRuleAction $createRuleAction)
    {
        $this->authorize(Rule::class);

        $rule = $createRuleAction->execute($this->user(), RuleData::fromRequest($request));

        return RuleResource::make($rule);
    }

    public function update(RuleRequest $request, Rule $rule, UpdateRuleAction $updateRuleAction)
    {
        $this->authorize($rule);

        $rule = $updateRuleAction->execute($rule, RuleData::fromRequest($request));

        return RuleResource::make($rule);
    }

    public function destroy(Rule $rule, DeleteRuleAction $deleteRuleAction)
    {
        $this->authorize($rule);

        $deleteRuleAction->execute($rule);

        return $this->noContent();
    }
}
