<?php

use App\Http\Loophole\Controllers\Activity\ActivityLogController;
use App\Http\Loophole\Controllers\PingController;
use App\Http\Loophole\Controllers\Recipient\AdministrativelyDisablePhonecallRecipientController;
use App\Http\Loophole\Controllers\Statistics\HostUsageStatisticsController;
use App\Http\Loophole\Controllers\StatusPage\StatusPageBadgeController;
use App\Http\Loophole\Controllers\StatusPage\StatusPageController;
use App\Http\Loophole\Controllers\StatusPage\StatusPageHistoryController;
use App\Http\Loophole\Controllers\StatusPage\StatusPageImageController;
use App\Http\Loophole\Controllers\StatusPage\StatusPageShieldController;
use App\Http\Loophole\Controllers\Team\TeamPlanManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', PingController::class);

Route::get('/teams/{team}/usage', HostUsageStatisticsController::class);
Route::patch('/teams/{team}', TeamPlanManagementController::class);
Route::put('/administratively-disable-phonecall-recipient/{sendto}', AdministrativelyDisablePhonecallRecipientController::class);

Route::post('/activity', ActivityLogController::class);

Route::get('/status-pages/{statusPage:token}/image', StatusPageImageController::class);
Route::get('/status-pages/{statusPage:token}/history', StatusPageHistoryController::class);
Route::get('/status-pages/{statusPage:token}/badge', StatusPageBadgeController::class);
Route::get('/status-pages/{statusPage:token}/shield', StatusPageShieldController::class);
Route::get('/status-pages/{statusPage:token}', StatusPageController::class);
