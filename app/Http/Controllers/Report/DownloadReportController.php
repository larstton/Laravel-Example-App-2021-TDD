<?php

namespace App\Http\Controllers\Report;

use App\Actions\Report\CreateEventReportAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Report\ReportRequest;
use App\Http\Resources\Report\ReportResource;
use Illuminate\Support\Carbon;

class DownloadReportController extends Controller
{
    public function __invoke(ReportRequest $request, CreateEventReportAction $createEventReportAction)
    {
        $from = Carbon::createFromTimestamp($request->filter['from']);
        $to = Carbon::createFromTimestamp($request->filter['to']);

        $report = $createEventReportAction->execute(current_user(), $from, $to,
            [
                'search' => null,
                'host'   => optional($request->filter)['host'],
            ]
        );

        return ReportResource::collection($report);
    }
}
