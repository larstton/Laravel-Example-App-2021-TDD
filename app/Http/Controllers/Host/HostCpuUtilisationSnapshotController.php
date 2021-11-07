<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Http\Resources\Host\HostCpuUtilisationSnapshotResource;
use App\Models\Host;

class HostCpuUtilisationSnapshotController extends Controller
{
    public function __invoke(Host $host)
    {
        return HostCpuUtilisationSnapshotResource::collection(
            $host->cpuUtilisationSnapshots()->latest('id')->jsonPaginate()
        );
    }
}
