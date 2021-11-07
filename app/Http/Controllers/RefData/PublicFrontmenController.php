<?php

namespace App\Http\Controllers\RefData;

use App\Http\Controllers\Controller;
use App\Http\Resources\Frontman\PublicFrontmanResource;
use App\Models\Frontman;
use Illuminate\Http\Request;

class PublicFrontmenController extends Controller
{
    public function __invoke(Request $request)
    {
        return PublicFrontmanResource::collection(
            Frontman::withoutTeamScope()->public()->get()
        );
    }
}
