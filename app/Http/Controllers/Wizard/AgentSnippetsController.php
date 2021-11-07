<?php

namespace App\Http\Controllers\Wizard;

use App\Actions\Wizard\MakeAgentSnippets;
use App\Http\Controllers\Controller;
use App\Models\Host;

class AgentSnippetsController extends Controller
{
    public function __invoke(Host $host, MakeAgentSnippets $makeAgentSnippets)
    {
        return $this->json([
            'data' => $makeAgentSnippets->execute($host),
        ]);
    }
}
