<?php

namespace App\Http\Controllers\Report;

use App\Actions\Report\CreateEventReportAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Report\ReportRequest;
use App\Http\Resources\Report\ReportResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function __invoke(ReportRequest $request, CreateEventReportAction $createEventReportAction)
    {
        $from = Carbon::createFromTimestamp($request->filter['from']);
        $to = Carbon::createFromTimestamp($request->filter['to']);

        $report = $createEventReportAction->execute(current_user(), $from, $to,
            [
                'search' => $request->search,
                'host'   => optional($request->filter)['host'],
            ]
        );

        $currentPage = (int)$request->input('page.number', 1);
        $perPage = (int)$request->input('page.size', 10);

        return ReportResource::collection(new LengthAwarePaginator(
            $report->forPage($currentPage, $perPage),
            $report->count(),
            $perPage,
            $currentPage
        ));
    }
}
