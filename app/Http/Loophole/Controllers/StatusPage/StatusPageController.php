<?php

namespace App\Http\Loophole\Controllers\StatusPage;

use App\Http\Controllers\Controller;
use App\Models\StatusPage;
use App\Support\StatusPage\StatusPageBuilder;

class StatusPageController extends Controller
{
    public function __invoke(StatusPage $statusPage)
    {
        return StatusPageBuilder::for($statusPage)->toResponse();
    }
}
