<?php

namespace App\Http\Controllers\Rule;

use App\Actions\Rule\FetchAgentProcessListAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AgentProcessesController extends Controller
{
    public function __invoke(Request $request, FetchAgentProcessListAction $fetchAgentProcessListAction, $type)
    {
        $processes = $fetchAgentProcessListAction->execute(current_team(), $type);

        return $this->success([
            'data' => $processes,
        ]);
    }
}
