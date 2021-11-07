<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Models\Host;
use App\Models\Tag;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class HostGroupController extends Controller
{
    public function __invoke()
    {
        return $this->json([
            'data' => Host::getGroupListForActiveHosts()
                ->groupBy(fn (Tag $tag) => Str::before($tag->name, ':'))
                ->keys(),
        ]);
    }
}
