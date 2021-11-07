<?php

namespace App\Actions\StatusPage;

use App\Models\StatusPage;

class DeleteStatusPageAction
{
    public function execute(StatusPage $statusPage): void
    {
        $statusPage->delete();
    }
}
