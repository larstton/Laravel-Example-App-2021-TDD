<?php

namespace App\Http\Controllers\Wizard;

use App\Actions\Wizard\MakeFrontmanSnippets;
use App\Http\Controllers\Controller;
use App\Models\Frontman;

class FrontmanSnippetsController extends Controller
{
    public function __invoke(Frontman $frontman, MakeFrontmanSnippets $makeFrontmanSnippets)
    {
        return $this->json([
            'data' => $makeFrontmanSnippets->execute($frontman),
        ]);
    }
}
