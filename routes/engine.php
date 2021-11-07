<?php

use App\Http\Controllers\Activity\HostActivityController;
use App\Http\Controllers\Activity\TeamActivityController;
use App\Http\Controllers\ApiToken\ApiTokenController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\JoinTeamController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RefreshController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Checkout\CheckoutDataController;
use App\Http\Controllers\Event\EventCommentController;
use App\Http\Controllers\Event\EventController;
use App\Http\Controllers\Frontman\FrontmanController;
use App\Http\Controllers\Frontman\FrontmanSummaryListController;
use App\Http\Controllers\Graph\GraphDataAliasesController;
use App\Http\Controllers\Graph\GraphDataController;
use App\Http\Controllers\Graph\ListMonitoredMetricsForHostController;
use App\Http\Controllers\Host\ConnectController;
use App\Http\Controllers\Host\ConnectValidationController;
use App\Http\Controllers\Host\GroupAggregatedHostDataController;
use App\Http\Controllers\Host\HostAggregatedEventsController;
use App\Http\Controllers\Host\HostAlertHistoryController;
use App\Http\Controllers\Host\HostController;
use App\Http\Controllers\Host\HostCpuUtilisationSnapshotController;
use App\Http\Controllers\Host\HostCustomCheckController;
use App\Http\Controllers\Host\HostEventsController;
use App\Http\Controllers\Host\HostGroupController;
use App\Http\Controllers\Host\HostHashController;
use App\Http\Controllers\Host\HostJobmonResultController;
use App\Http\Controllers\Host\HostLatestDataController;
use App\Http\Controllers\Host\HostServiceCheckController;
use App\Http\Controllers\Host\HostSnmpCheckController;
use App\Http\Controllers\Host\HostSummaryListController;
use App\Http\Controllers\Host\HostTagController;
use App\Http\Controllers\Host\HostWebCheckController;
use App\Http\Controllers\Host\PurgeHostFromReportsController;
use App\Http\Controllers\Host\SubUnitAggregatedHostDataController;
use App\Http\Controllers\Onboard\OnboardingController;
use App\Http\Controllers\Recipient\RecipientController;
use App\Http\Controllers\Recipient\RecipientLogController;
use App\Http\Controllers\Recipient\ResendVerificationEmailController;
use App\Http\Controllers\Recipient\SendTestMessageController;
use App\Http\Controllers\RefData\PublicFrontmenController;
use App\Http\Controllers\RefData\TimezonesController;
use App\Http\Controllers\Report\DownloadReportController;
use App\Http\Controllers\Report\ReportController;
use App\Http\Controllers\Rule\AgentProcessesController;
use App\Http\Controllers\Rule\RuleController;
use App\Http\Controllers\Rule\RulePositionController;
use App\Http\Controllers\StatusPage\StatusPageController;
use App\Http\Controllers\StatusPage\StatusPageImageController;
use App\Http\Controllers\SubUnit\SubUnitController;
use App\Http\Controllers\Articles\ArticleSearchController;
use App\Http\Controllers\Articles\ArticleController;
use App\Http\Controllers\Support\SupportRequestController;
use App\Http\Controllers\Team\MarketingController;
use App\Http\Controllers\Team\ResendTeamInvitationController;
use App\Http\Controllers\Team\TeamController;
use App\Http\Controllers\Team\TeamMemberController;
use App\Http\Controllers\Team\TeamSettingController;
use App\Http\Controllers\TooltipController;
use App\Http\Controllers\User\ChangePasswordController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\UserGeoIPController;
use App\Http\Controllers\User\UserSettingController;
use App\Http\Controllers\ValidationController;
use App\Http\Controllers\Wizard\AgentSnippetsController;
use App\Http\Controllers\Wizard\FrontmanSnippetsController;
use App\Http\Controllers\Wizard\WebCheckWizardController;
use Illuminate\Support\Facades\Route;

Route::prefix('/auth')
    ->group(function () {
        Route::middleware('guest')->group(function () {
            Route::post('register', RegisterController::class);
            Route::post('join-team', JoinTeamController::class);
            Route::post('login', LoginController::class);
            Route::post('recovery', ForgotPasswordController::class);
            Route::post('reset', ResetPasswordController::class);
        });

        Route::middleware('auth:engine')->group(function () {
            Route::post('verify/resend', VerificationController::class)
                ->middleware(['throttle:email-verification'])
                ->name('email.verify.resend');
            Route::post('logout', LogoutController::class)->name('logout');
        });

        Route::post('refresh', RefreshController::class);
    });

Route::middleware('auth:engine')->group(function () {
    Route::prefix('user')->name('user.')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::put('/', [UserController::class, 'update']);
        Route::put('{user}', [UserController::class, 'update']);
        Route::delete('{user}', [UserController::class, 'destroy']);
        Route::post('password', ChangePasswordController::class);
        Route::get('geo', UserGeoIPController::class);
    });

    Route::post('onboard/{step}', OnboardingController::class)
        ->where('step', '1|2');

    Route::post('/marketing-emails/{user}/{team}', MarketingController::class);

    Route::prefix('team')->name('team.')->group(function () {
        Route::get('/', [TeamController::class, 'index']);
        Route::put('/', [TeamController::class, 'update']);
        Route::put('{team}', [TeamController::class, 'update']);
    });

    Route::put('team-settings', TeamSettingController::class);
    Route::put('user-settings', UserSettingController::class);

    Route::prefix('hosts/{host}')->name('host.')->group(function () {
        Route::get('latest-data', HostLatestDataController::class)
            ->name('latest-data');
        Route::get('jobmon-results', [HostJobmonResultController::class, 'index'])
            ->name('jobmon-results');
        Route::get('jobmon-results/{jobId}', [HostJobmonResultController::class, 'show'])
            ->name('jobmon-results.show');
        Route::delete('jobmon-results/{jobId}', [HostJobmonResultController::class, 'destroy'])
            ->name('jobmon-results.destroy');
        Route::get('cpu-utilisation-snapshots', HostCpuUtilisationSnapshotController::class)
            ->name('cpu-utilisation-snapshots');
    });

    Route::prefix('hosts')->name('host.')->group(function () {
        Route::get('list', HostSummaryListController::class);
        Route::get('summary-list', HostSummaryListController::class);
        Route::get('connect/{connect}/validate', ConnectValidationController::class);
        Route::get('connect/{connect}', ConnectController::class);
        Route::get('update-check/{hostsHash}', HostHashController::class);
        Route::get('tags', HostTagController::class);
        Route::get('groups', HostGroupController::class);
        Route::get('aggregated/group', GroupAggregatedHostDataController::class);
        Route::get('aggregated/sub-unit', SubUnitAggregatedHostDataController::class);
        Route::get('aggregated/{aggregateBy}/events', HostAggregatedEventsController::class)
            ->where(['aggregateBy' => 'group|sub-unit']);
        Route::get('{host}/alert-history', HostAlertHistoryController::class);
        Route::prefix('{host}/graph-data')->name('graph.')->group(function () {
            Route::get('keys', ListMonitoredMetricsForHostController::class)->name('keys');
            Route::get('/', GraphDataController::class)->name('data');
        });
    });

    Route::apiResource('hosts', HostController::class);
    Route::apiResource('hosts.service-checks', HostServiceCheckController::class)->except('show');
    Route::apiResource('hosts.snmp-checks', HostSnmpCheckController::class)->except('show');
    Route::apiResource('hosts.custom-checks', HostCustomCheckController::class)->except('show');
    Route::apiResource('hosts.web-checks', HostWebCheckController::class)->except('show');
    Route::apiResource('hosts.events', HostEventsController::class)->except('show');

    Route::get('graph-data', GraphDataController::class);
    Route::get('graph-aliases', GraphDataAliasesController::class);

    Route::apiResource('rules', RuleController::class)->except('show');
    Route::prefix('rules')->name('rules.')->group(function () {
        Route::patch('{rule}/position', RulePositionController::class);
        Route::get('hosts', HostSummaryListController::class);
        Route::get('processes/{type}', AgentProcessesController::class)
            ->where('type', 'process|cmdline');
    });

    Route::apiResource('recipients', RecipientController::class);
    Route::prefix('recipients')->name('recipient.')->group(function () {
        Route::get('{recipient}/logs', RecipientLogController::class);
        Route::post('{recipient}/resend-verification-email', ResendVerificationEmailController::class)
            ->middleware(['throttle:email-verification']);
        Route::post('send-test-message', SendTestMessageController::class)
            ->middleware(['throttle:sending-test-message']);
    });

    Route::apiResource('team-members', TeamMemberController::class);
    Route::get('team-members/{teamMember}/resend-invitation', ResendTeamInvitationController::class)
        ->middleware(['throttle:email-verification']);

    Route::apiResource('sub-units', SubUnitController::class);

    Route::apiResource('api-tokens', ApiTokenController::class)->except('update');

    Route::get('frontmen/summary-list', FrontmanSummaryListController::class);
    Route::apiResource('frontmen', FrontmanController::class);

    Route::apiResource('support-requests', SupportRequestController::class)->except('destroy');

    Route::apiResource('events', EventController::class)->except('store', 'show');
    Route::apiResource('events.comments', EventCommentController::class)->only('index', 'store');

    Route::apiResource('status-pages', StatusPageController::class);
    Route::prefix('status-pages')->name('status-pages.')->group(function () {
        Route::get('{statusPage}/image', [StatusPageImageController::class, 'show']);
        Route::post('{statusPage}/image', [StatusPageImageController::class, 'store']);
        Route::delete('{statusPage}/image', [StatusPageImageController::class, 'destroy']);
    });

    Route::prefix('ref-data')->name('ref-data.')->group(function () {
        Route::get('timezones', TimezonesController::class);
        Route::get('frontmen', PublicFrontmenController::class);
    });

    Route::prefix('wizard')->name('wizard.')->group(function () {
        Route::get('frontman/{frontman}', FrontmanSnippetsController::class);
        Route::get('agent/{host}', AgentSnippetsController::class);
        Route::post('web-check', WebCheckWizardController::class);
    });

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', ReportController::class);
        Route::patch('purge-host/{hostId}', PurgeHostFromReportsController::class);
        Route::get('download', DownloadReportController::class);
    });

    Route::prefix('activity')->name('activity.')->group(function () {
        Route::get('hosts', HostActivityController::class);
        Route::get('team', TeamActivityController::class);
    });

    Route::post('validate/{entity}', ValidationController::class);

    Route::get('checkout-data', CheckoutDataController::class);

    Route::prefix('articles')->name('articles.')->group(function () {
        Route::get('/', ArticleController::class);
    });
});

Route::get('tooltips/{file}', TooltipController::class)
    ->where('file', '.*');
