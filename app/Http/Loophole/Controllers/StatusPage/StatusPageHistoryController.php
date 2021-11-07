<?php

namespace App\Http\Loophole\Controllers\StatusPage;

use App\Http\Controllers\Controller;
use App\Models\StatusPage;
use App\Support\StatusPage\StatusPageBuilder;

class StatusPageHistoryController extends Controller
{
    public function __invoke(StatusPage $statusPage, $type = null)
    {
        return StatusPageBuilder::getHistoryFor($statusPage)->toResponse();
    }
}
