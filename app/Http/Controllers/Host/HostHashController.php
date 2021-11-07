<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Models\Host;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HostHashController extends Controller
{
    public function __invoke(Request $request, $hostsHash)
    {
        $hash = Host::getHashOfAllTeamsHosts();

        if (Str::of($hostsHash)->trim()->lower()->is($hash)) {
            return $this->noContent();
        }

        return $this->json([
            'data' => ['updateAvailable' => true],
        ]);
    }
}
