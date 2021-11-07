<?php

namespace App\Http\Controllers\Host;

use App\Actions\Host\GetHostAlertLogFromNotifierAction;
use App\Http\Controllers\Controller;
use App\Models\Host;
use Illuminate\Http\Request;

class HostAlertHistoryController extends Controller
{
    public function __invoke(Request $request, Host $host, GetHostAlertLogFromNotifierAction $action)
    {
        $this->validate($request, [
            'filter.from' => ['required', 'integer'],
            'filter.to'   => ['required', 'integer'],
            'page.size'   => ['sometimes', 'nullable', 'integer'],
        ]);

        $data = $action->execute($host, $request->only(['filter', 'page']));

        return $this->json(compact('data'));
    }
}
