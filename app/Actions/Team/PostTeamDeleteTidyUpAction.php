<?php

namespace App\Actions\Team;

use App\Models\CustomCheck;
use App\Models\Team;
use App\Models\User;
use App\Support\CheckoutService;
use App\Support\Influx\InfluxRepository;
use App\Support\NotifierService;
use App\Support\Tenancy\Facades\TenantManager;
use Illuminate\Support\Facades\DB;

class PostTeamDeleteTidyUpAction
{
    private InfluxRepository $influxRepository;
    private NotifierService $notifierService;
    private CheckoutService $checkoutService;
    private $relationsToDelete = [
        'customChecks',
        'webChecks',
        'snmpChecks',
        'serviceChecks',
        'reminders',
        'events',
        'eventComments',
        'checkResults',
        'teamSettings',
        'userSettings',
        'rules',
        'recipients',
        'frontmen',
        'hostHistories',
        'teamMembers',
        'apiTokens',
        'paidMessageLog',
        'subUnits',
        'statusPages',
        'jobMonResults',
        'hosts',
        'activityLogs',
        'userAgentData',
    ];

    public function __construct(
        InfluxRepository $influxRepository,
        NotifierService $notifierService,
        CheckoutService $checkoutService
    ) {
        $this->influxRepository = $influxRepository;
        $this->notifierService = $notifierService;
        $this->checkoutService = $checkoutService;
    }

    public function execute(Team $team): void
    {
        TenantManager::disableTenancyChecks();

        $team->admins->each(function (User $admin) {
            DB::table('deleted_users')->insert([
                'email'        => $admin->email,
                'product_news' => $admin->product_news,
                'updated_at'   => now(),
                'created_at'   => now(),
            ]);
        });

        $team->customChecks->each(function (CustomCheck $customCheck) {
            $this->influxRepository->setDatabase(
                config('influx.repository.databases.customChecks')
            )->dropMeasurement($customCheck->id);
        });
        $this->notifierService->deleteTeam($team);
        $this->checkoutService->deleteTeam($team);

        DB::table('team_statistics')->where('team_id', $team->id)->delete();

        collect($this->relationsToDelete)
            ->each(function ($relationMethod) use ($team) {
                $team->$relationMethod()->delete();
            });
    }
}
