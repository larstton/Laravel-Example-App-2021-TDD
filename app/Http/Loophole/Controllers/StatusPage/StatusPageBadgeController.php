<?php

namespace App\Http\Loophole\Controllers\StatusPage;

use App\Http\Controllers\Controller;
use App\Models\StatusPage;
use App\Support\StatusPage\StatusPageBuilder;

class StatusPageBadgeController extends Controller
{
    public function __invoke(StatusPage $statusPage, $type = null)
    {
        return StatusPageBuilder::getBadgeFor($statusPage)->toResponse();
    }
}
