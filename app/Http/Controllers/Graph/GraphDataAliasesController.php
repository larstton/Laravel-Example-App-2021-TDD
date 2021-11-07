<?php

namespace App\Http\Controllers\Graph;

use App\Http\Controllers\Controller;
use App\Support\Influx\AliasResolver;
use Illuminate\Http\Request;

class GraphDataAliasesController extends Controller
{
    public function __invoke(Request $request)
    {
        return $this->json([
            'data' => AliasResolver::getMetricAliasList()->keys(),
        ]);
    }
}
