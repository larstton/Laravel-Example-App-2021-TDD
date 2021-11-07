<?php

namespace Actions\Team;

use App\Actions\Team\PostTeamDeleteTidyUpAction;
use App\Enums\TeamMemberRole;
use App\Events\CustomCheck\CustomCheckDeleted;
use App\Events\Event\EventDeleted;
use App\Events\Frontman\FrontmanDeleted;
use App\Events\JobmonResult\JobmonResultDeleted;
use App\Events\Recipient\RecipientDeleted;
use App\Events\Rule\RuleDeleted;
use App\Events\ServiceCheck\ServiceCheckDeleted;
use App\Events\SnmpCheck\SnmpCheckDeleted;
use App\Events\WebCheck\WebCheckDeleted;
use App\Models\ActivityLog;
use App\Models\ApiToken;
use App\Models\CheckResult;
use App\Models\CustomCheck;
use App\Models\Event;
use App\Models\EventComment;
use App\Models\Frontman;
use App\Models\Host;
use App\Models\HostHistory;
use App\Models\JobmonResult;
use App\Models\PaidMessageLog;
use App\Models\Recipient;
use App\Models\Reminder;
use App\Models\Rule;
use App\Models\ServiceCheck;
use App\Models\SnmpCheck;
use App\Models\StatusPage;
use App\Models\SubUnit;
use App\Models\TeamMember;
use App\Models\UserAgentData;
use App\Models\WebCheck;
use App\Support\CheckoutService;
use App\Support\Influx\InfluxRepository;
use App\Support\NotifierService;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event as EventDispatcher;
use Tests\TestCase;

class PostTeamDeleteTidyUpActionTest extends TestCase
{
    use WithoutEvents;

    /** @test */
    public function will_tidy_up_entities_after_deleting_team()
    {
        $this->buildEntities();

        $this->influxRepository
            ->shouldReceive('setDatabase->dropMeasurement')
            ->twice()
            ->andReturnUndefined();

        resolve(PostTeamDeleteTidyUpAction::class)->execute($this->team);

        $this->teamMembers->each(function (TeamMember $teamMember) {
            $this->assertDeleted($teamMember);
        });
        $this->customChecks->each(function (CustomCheck $customCheck) {
            $this->assertDeleted($customCheck);
        });
        $this->webChecks->each(function (WebCheck $webCheck) {
            $this->assertDeleted($webCheck);
        });
        $this->snmpChecks->each(function (SnmpCheck $snmpCheck) {
            $this->assertDeleted($snmpCheck);
        });
        $this->serviceChecks->each(function (ServiceCheck $serviceCheck) {
            $this->assertDeleted($serviceCheck);
        });
        $this->reminders->each(function (Reminder $reminder) {
            $this->assertDeleted($reminder);
        });
        $this->events->each(function (Event $event) {
            $this->assertDeleted($event);
        });
        $this->eventComments->each(function (EventComment $eventComment) {
            $this->assertDeleted($eventComment);
        });
        $this->checkResults->each(function (CheckResult $checkResult) {
            $this->assertDeleted($checkResult);
        });
        $this->assertDatabaseMissing('team_settings', [
            'team_id' => $this->team->id,
        ]);
        $this->assertDatabaseMissing('user_settings', [
            'user_id' => $this->user->id,
        ]);
        $this->rules->each(function (Rule $rule) {
            $this->assertDeleted($rule);
        });
        $this->recipients->each(function (Recipient $recipient) {
            $this->assertDeleted($recipient);
        });
        $this->frontmen->each(function (Frontman $frontman) {
            $this->assertDeleted($frontman);
        });
        $this->hostHistories->each(function (HostHistory $hostHistory) {
            $this->assertSoftDeleted($hostHistory);
        });
        $this->teamMembers->each(function (TeamMember $teamMember) {
            $this->assertDeleted($teamMember);
        });
        $this->apiTokens->each(function (ApiToken $apiToken) {
            $this->assertDeleted($apiToken);
        });
        $this->assertDatabaseMissing('paid_message_log', [
            'team_id' => $this->team->id,
        ]);
        $this->subUnits->each(function (SubUnit $subUnit) {
            $this->assertDeleted($subUnit);
        });
        $this->statusPages->each(function (StatusPage $statusPage) {
            $this->assertDeleted($statusPage);
        });
        $this->jobMonResults->each(function (JobmonResult $jobMonResult) {
            $this->assertDeleted($jobMonResult);
        });
        $this->hosts->each(function (Host $host) {
            $this->assertSoftDeleted($host);
        });
        $this->activityLogs->each(function (ActivityLog $activityLog) {
            $this->assertDeleted($activityLog);
        });
        $this->userAgentData->each(function (UserAgentData $userAgentData) {
            $this->assertDeleted($userAgentData);
        });
        $this->assertDatabaseMissing('team_statistics', [
            'team_id' => $this->team->id,
        ]);
    }

    /** @test */
    public function will_dispatch_deleted_events()
    {
        $this->buildEntities();

        $this->influxRepository
            ->shouldReceive('setDatabase->dropMeasurement')
            ->twice()
            ->andReturnUndefined();

        resolve(PostTeamDeleteTidyUpAction::class)->execute($this->team);

        EventDispatcher::assertDispatchedTimes(CustomCheckDeleted::class, 2);
        EventDispatcher::assertDispatchedTimes(WebCheckDeleted::class, 2);
        EventDispatcher::assertDispatchedTimes(SnmpCheckDeleted::class, 2);
        EventDispatcher::assertDispatchedTimes(ServiceCheckDeleted::class, 2);
        EventDispatcher::assertDispatchedTimes(EventDeleted::class, 2);
        EventDispatcher::assertDispatchedTimes(RuleDeleted::class, 2);
        EventDispatcher::assertDispatchedTimes(RecipientDeleted::class, 2);
        EventDispatcher::assertDispatchedTimes(FrontmanDeleted::class, 2);
        EventDispatcher::assertDispatchedTimes(FrontmanDeleted::class, 2);
        EventDispatcher::assertDispatchedTimes(JobmonResultDeleted::class, 2);
    }

    private function buildEntities()
    {
        $this->team = $this->createTeam();
        $this->user = $this->createUser($this->team);
        $this->teamMembers = TeamMember::factory()->for($this->team)->count(4)
            ->state(new Sequence(
                ['role' => TeamMemberRole::Admin()],
                ['role' => TeamMemberRole::Member()],
                ['role' => TeamMemberRole::Guest()],
                ['role' => TeamMemberRole::Deleted()],
            ))
            ->create();
        $this->hosts = Host::factory()->for($this->team)->count(2)->create();
        $host = $this->hosts->first();
        $this->customChecks = CustomCheck::factory()->for($host)->count(2)->create();
        $this->webChecks = WebCheck::factory()->for($host)->count(2)->create();
        $this->snmpChecks = SnmpCheck::factory()->for($host)->count(2)->create();
        $this->serviceChecks = ServiceCheck::factory()->for($host)->count(2)->create();
        $this->events = Event::factory()->for($this->team)->for($host)->count(2)->create();
        $this->reminders = Reminder::factory()->for($this->events->first())->count(2)->create();
        $this->eventComments = EventComment::factory()->for($this->team)->count(2)->create();
        $this->checkResults = CheckResult::factory()->for($host)->count(2)->create();

        config([
            'settings-team-settings.test-value' => true,
        ]);
        team_settings($this->team)->set([
            'test-value' => false,
        ]);
        config([
            'settings-user-settings.test-value' => true,
        ]);
        user_settings($this->user)->set([
            'test-value' => false,
        ]);

        $this->rules = Rule::factory()->for($this->team)->count(2)->create();
        $this->recipients = Recipient::factory()->for($this->team)->count(2)->create();
        $this->frontmen = Frontman::factory()->for($this->team)->count(2)->create();
        $this->hostHistories = HostHistory::factory()->for($this->team)->count(2)->create();
        $this->apiTokens = ApiToken::factory()->for($this->team)->count(2)->create();
        $this->paidMessageLog = PaidMessageLog::factory()->for($this->team)->count(2)->create();
        $this->subUnits = SubUnit::factory()->for($this->team)->count(2)->create();
        $this->statusPages = StatusPage::factory()->for($this->team)->count(2)->create();
        $this->jobMonResults = JobmonResult::factory()->for($host)->count(2)->create();
        $this->activityLogs = ActivityLog::factory()->for($this->team)->count(2)->create();
        $this->userAgentData = UserAgentData::factory()->for($this->team)->count(2)->create();

        DB::table('team_statistics')->insert([
            'team_id'            => $this->team->id,
            'key'                => 'key',
            'value'              => 100,
            'last_summary_total' => 10,
        ]);
    }

    /** @test */
    public function will_drop_measurements_from_influx_for_all_custom_checks()
    {
        $team = $this->createTeam();
        $host = Host::factory()->for($team)->create();
        $customChecks = CustomCheck::factory()->for($host)->count(2)->create();

        $customChecks->each(function(CustomCheck $customCheck) {
            $this->influxRepository
                ->shouldReceive('setDatabase', [config('influx.repository.databases.customChecks')])
                ->andReturnSelf()
                ->shouldReceive('dropMeasurement', [$customCheck->id])
                ->andReturnUndefined();
        });

        resolve(PostTeamDeleteTidyUpAction::class)->execute($team);
    }

    /** @test */
    public function will_ping_notifier_to_delete_team()
    {
        $team = $this->createTeam();

        $this->notifierService
            ->shouldReceive('deleteTeam', [$team])
            ->andReturnTrue();

        resolve(PostTeamDeleteTidyUpAction::class)->execute($team);
    }

    /** @test */
    public function will_ping_checkout_to_delete_team()
    {
        $team = $this->createTeam();

        $this->checkoutService
            ->shouldReceive('deleteTeam', [$team])
            ->andReturnTrue();

        resolve(PostTeamDeleteTidyUpAction::class)->execute($team);
    }

    /** @test */
    public function will_record_team_admins_to_deleted_users_table_prior_to_deleting()
    {
        $team = $this->createTeam();
        $teamAdmins = TeamMember::factory()->for($team)->count(2)
            ->state(new Sequence(
                ['product_news' => true],
                ['product_news' => false],
            ))
            ->create([
                'role' => TeamMemberRole::Admin(),
            ]);

        Carbon::setTestNow($now = now());

        resolve(PostTeamDeleteTidyUpAction::class)->execute($team);

        $teamAdmins->each(function (TeamMember $admin) use ($now) {
            $this->assertDatabaseHas('deleted_users', [
                'email'        => $admin->email,
                'product_news' => $admin->product_news,
                'updated_at'   => $now,
                'created_at'   => $now,
            ]);
        });
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->notifierService = $this->mock(NotifierService::class)->shouldIgnoreMissing();
        $this->checkoutService = $this->mock(CheckoutService::class)->shouldIgnoreMissing();
        $this->influxRepository = $this->mock(InfluxRepository::class)->shouldIgnoreMissing();
    }
}
