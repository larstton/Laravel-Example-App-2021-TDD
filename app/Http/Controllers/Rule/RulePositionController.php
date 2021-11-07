<?php

namespace App\Http\Controllers\Rule;

use App\Http\Controllers\Controller;
use App\Models\Rule;
use Illuminate\Http\Request;

class RulePositionController extends Controller
{
    public function __invoke(Request $request, Rule $rule)
    {
        $request->validate([
            'position' => 'required|int',
        ]);

        $rules = Rule::ordered()
            ->get('id')
            ->pluck('id')
            ->reject(fn ($id) => $id === $rule->id)
            ->toArray();

        array_splice($rules, $request->position - 1, 0, $rule->id);

        Rule::setNewOrder($rules);

        return $this->accepted();
    }
}
