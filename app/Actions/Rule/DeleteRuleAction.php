<?php

namespace App\Actions\Rule;

use App\Models\Event;
use App\Models\Rule;
use Illuminate\Support\Facades\DB;

class DeleteRuleAction
{
    public function execute(Rule $rule)
    {
        DB::transaction(function () use ($rule) {
            if ($rule->delete()) {
                Event::whereRuleId($rule->id)
                    ->each(function (Event $event) {
                        $event->delete();
                    });
            }
        });
    }
}
