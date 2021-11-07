<?php

namespace Tests\Unit\Providers;

use App\Events\Auth\NewTeamCreated;
use App\Events\Auth\NewUserRegistered;
use App\Events\Auth\UserLoggedIn;
use App\Events\Auth\UserLoggedOut;
use App\Events\CustomCheck\CustomCheckCreated;
use App\Events\CustomCheck\CustomCheckDeleted;
use App\Events\CustomCheck\CustomCheckUpdated;
use App\Events\Event\EventCommentCreated;
use App\Events\Event\EventCreated;
use App\Events\Event\EventDeleted;
use App\Events\Event\EventUpdated;
use App\Events\Frontman\FrontmanCreated;
use App\Events\Frontman\FrontmanDeleted;
use App\Events\Frontman\FrontmanUpdated;
use App\Events\Host\HostCreated;
use App\Events\Host\HostDeleted;
use App\Events\Host\HostUpdated;
use App\Events\JobmonResult\JobmonResultCreated;
use App\Events\JobmonResult\JobmonResultDeleted;
use App\Events\JobmonResult\JobmonResultUpdated;
use App\Events\Recipient\RecipientCreated;
use App\Events\Recipient\RecipientDeleted;
use App\Events\Recipient\RecipientUnsubscribedFromDailySummary;
use App\Events\Recipient\RecipientUpdated;
use App\Events\Recipient\RecipientVerified;
use App\Events\Rule\RuleCreated;
use App\Events\Rule\RuleDeleted;
use App\Events\Rule\RuleUpdated;
use App\Events\ServiceCheck\ServiceCheckCreated;
use App\Events\ServiceCheck\ServiceCheckDeleted;
use App\Events\ServiceCheck\ServiceCheckUpdated;
use App\Events\SnmpCheck\SnmpCheckCreated;
use App\Events\SnmpCheck\SnmpCheckDeleted;
use App\Events\SnmpCheck\SnmpCheckUpdated;
use App\Events\Support\SupportMessageCreated;
use App\Events\Team\TeamMemberInvited;
use App\Events\Team\TeamPlanDowngraded;
use App\Events\Team\TeamPlanUpgraded;
use App\Events\Team\TeamSettingsUpdated;
use App\Events\User\UserSettingsUpdated;
use App\Events\User\UserUpdated;
use App\Events\User\UserUpdatedPassword;
use App\Events\WebCheck\WebCheckCreated;
use App\Events\WebCheck\WebCheckDeleted;
use App\Events\WebCheck\WebCheckUpdated;
use App\Listeners\Auth\LogAgentData;
use App\Listeners\Auth\RecordLoginEventToTeamActivity;
use App\Listeners\Auth\SendEmailVerificationNotification;
use App\Listeners\Event\NotifyEventSubscribersOfNewComment;
use App\Listeners\Event\RecoverDeletedEvent;
use App\Listeners\Host\AddNewHostToHistoryLog;
use App\Listeners\Host\UpdateHostInHistoryLog;
use App\Listeners\Notifier\AddNewHostToNotifierService;
use App\Listeners\Notifier\UpdateHostOnNotifierService;
use App\Listeners\Recipient\ActivateRecipientOnEmailVerification;
use App\Listeners\Recipient\CreatedRecipientSetHostTagMeta;
use App\Listeners\Recipient\DeletedRecipientSetHostTagMeta;
use App\Listeners\Recipient\SendIntegromatInitMessage;
use App\Listeners\Recipient\SendRecipientEmailConfirmation;
use App\Listeners\Recipient\UpdatedRecipientSetHostTagMeta;
use App\Listeners\Settings\LogSettingsActivity;
use App\Listeners\Support\SendSupportRequestConfirmation;
use App\Listeners\Team\SendTeamMemberInvitation;
use App\Models\CustomCheck;
use App\Models\EventComment;
use App\Models\Frontman;
use App\Models\Host;
use App\Models\JobmonResult;
use App\Models\Recipient;
use App\Models\Rule;
use App\Models\ServiceCheck;
use App\Models\SnmpCheck;
use App\Models\SupportRequest;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use App\Models\WebCheck;
use CloudRadar\LaravelSettings\Event\SettingUpdated;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Tests\Concerns\EventHelpers;
use Tests\Concerns\WithoutTenancyChecks;
use Tests\TestCase;

class EventServiceProviderTest extends TestCase
{
    use EventHelpers, WithoutTenancyChecks;

    protected array $listeners;
    protected User $user;
    protected Team $team;
    protected Host $host;
    protected Frontman $frontman;
    protected WebCheck $webCheck;
    protected ServiceCheck $serviceCheck;
    protected SnmpCheck $snmpCheck;
    protected CustomCheck $customCheck;
    protected Rule $rule;
    protected Recipient $recipient;
    protected SupportRequest $supportRequest;
    protected TeamMember $teamMember;
    protected \App\Models\Event $event;
    protected EventComment $eventComment;
    protected JobmonResult $jobmonResult;

    /** @test */
    public function will_dispatch_correct_listeners_on_NewUserRegistered()
    {
        $this->expectsListeners([
            SendEmailVerificationNotification::class,
            LogAgentData::class,
        ]);

        NewUserRegistered::dispatch($this->user, []);
    }

    /** @test */
    public function will_dispatch_correct_listeners_for_UserVerifiedEmail()
    {
        $this->expectsListeners([
            ActivateRecipientOnEmailVerification::class,
        ]);

        event(new Verified($this->user));
    }

    /** @test */
    public function will_dispatch_correct_listeners_on_NewTeamCreated()
    {
        $this->expectsListeners([])->forExpectedEvent(NewTeamCreated::class);

        NewTeamCreated::dispatch($this->user, $this->team);
    }

    /** @test */
    public function will_dispatch_correct_listeners_on_UserLoggedIn()
    {
        $this->expectsListeners([
            RecordLoginEventToTeamActivity::class,
        ])->forExpectedEvent(UserLoggedIn::class);

        UserLoggedIn::dispatch($this->user);
    }

    /** @test */
    public function will_dispatch_correct_listeners_on_UserLoggedOut()
    {
        $this->expectsListeners([])->forExpectedEvent(UserLoggedOut::class);

        UserLoggedOut::dispatch($this->user);
    }

    /** @test */
    public function will_dispatch_correct_listeners_on_UserUpdated()
    {
        $this->expectsListeners([])->forExpectedEvent(UserUpdated::class);

        UserUpdated::dispatch($this->user);
    }

    /** @test */
    public function will_dispatch_correct_listeners_on_UserUpdatedPassword()
    {
        $this->expectsListeners([])->forExpectedEvent(UserUpdatedPassword::class);

        UserUpdatedPassword::dispatch($this->user);
    }

    /** @test */
    public function will_dispatch_correct_listeners_on_HostCreated()
    {
        $this->expectsListeners([
            AddNewHostToHistoryLog::class,
            AddNewHostToNotifierService::class,
        ])->forExpectedEvent(HostCreated::class);

        HostCreated::dispatch($this->host);
    }

    /** @test */
    public function will_dispatch_correct_listeners_on_HostUpdated()
    {
        $this->expectsListeners([
            UpdateHostInHistoryLog::class,
            UpdateHostOnNotifierService::class,
        ])->forExpectedEvent(HostUpdated::class);

        HostUpdated::dispatch($this->host);
    }

    /** @test */
    public function will_dispatch_correct_listeners_on_HostDeleted()
    {
        $this->expectsListeners([])->forExpectedEvent(HostDeleted::class);

        HostDeleted::dispatch($this->host);
    }

    /** @test */
    public function will_dispatch_correct_listeners_on_FrontmanCreated()
    {
        $this->expectsListeners([])->forExpectedEvent(FrontmanCreated::class);

        FrontmanCreated::dispatch($this->frontman);
    }

    /** @test */
    public function will_dispatch_correct_listeners_on_FrontmanUpdated()
    {
        $this->expectsListeners([])->forExpectedEvent(FrontmanUpdated::class);

        FrontmanUpdated::dispatch($this->frontman);
    }

    /** @test */
    public function will_dispatch_correct_listeners_on_FrontmanDeleted()
    {
        $this->expectsListeners([])->forExpectedEvent(FrontmanDeleted::class);

        FrontmanDeleted::dispatch($this->frontman);
    }

    /** @test */
    public function will_dispatch_correct_listeners_on_WebCheckCreated()
    {
        $this->expectsListeners([])->forExpectedEvent(WebCheckCreated::class);

        WebCheckCreated::dispatch($this->webCheck);
    }

    /** @test */
    public function will_dispatch_correct_listeners_on_WebCheckUpdated()
    {
        $this->expectsListeners([])->forExpectedEvent(WebCheckUpdated::class);

        WebCheckUpdated::dispatch($this->webCheck);
    }

    /** @test */
    public function will_dispatch_correct_listeners_on_WebCheckDeleted()
    {
        $this->expectsListeners([])->forExpectedEvent(WebCheckDeleted::class);

        WebCheckDeleted::dispatch($this->webCheck);
    }

    /** @test */
    public function will_dispatch_correct_listeners_on_ServiceCheckCreated()
    {
        $this->expectsListeners([])->forExpectedEvent(ServiceCheckCreated::class);

        ServiceCheckCreated::dispatch($this->serviceCheck);
    }

    /** @test */
    public function will_dispatch_correct_listeners_on_ServiceCheckUpdated()
    {
        $this->expectsListeners([])->forExpectedEvent(ServiceCheckUpdated::class);

        ServiceCheckUpdated::dispatch($this->serviceCheck);
    }

    /** @test */
    public function will_dispatch_correct_listeners_on_ServiceCheckDeleted()
    {
        $this->expectsListeners([])->forExpectedEvent(ServiceCheckDeleted::class);

        ServiceCheckDeleted::dispatch($this->serviceCheck);
    }

    /** @test */
    public function will_dispatch_correct_listeners_on_SnmpCheckCreated()
    {
        $this->expectsListeners([])->forExpectedEvent(SnmpCheckCreated::class);

        SnmpCheckCreated::dispatch($this->snmpCheck);
    }

    /** @test */
    public function will_dispatch_correct_listeners_on_SnmpCheckUpdated()
    {
        $this->expectsListeners([])->forExpectedEvent(SnmpCheckUpdated::class);

        SnmpCheckUpdated::dispatch($this->snmpCheck);
    }

    /** @test */
    public function will_dispatch_correct_listeners_on_SnmpCheckDeleted()
    {
        $this->expectsListeners([])->forExpectedEvent(SnmpCheckDeleted::class);

        SnmpCheckDeleted::dispatch($this->snmpCheck);
    }

    /** @test */
    public function will_dispatch_correct_listeners_on_CustomCheckCreated()
    {
        $this->expectsListeners([])->forExpectedEvent(CustomCheckCreated::class);

        CustomCheckCreated::dispatch($this->customCheck);
    }

    /** @test */
    public function will_dispatch_correct_listeners_on_CustomCheckUpdated()
    {
        $this->expectsListeners([])->forExpectedEvent(CustomCheckUpdated::class);

        CustomCheckUpdated::dispatch($this->customCheck);
    }

    /** @test */
    public function will_dispatch_correct_listeners_on_CustomCheckDeleted()
    {
        $this->expectsListeners([])->forExpectedEvent(CustomCheckDeleted::class);

        CustomCheckDeleted::dispatch($this->customCheck);
    }

    /** @test */
    public function will_dispatch_correct_listeners_on_RuleCreated()
    {
        $this->expectsListeners([])->forExpectedEvent(RuleCreated::class);

        RuleCreated::dispatch($this->rule);
    }

    /** @test */
    public function will_dispatch_correct_listeners_on_RuleUpdated()
    {
        $this->expectsListeners([])->forExpectedEvent(RuleUpdated::class);

        RuleUpdated::dispatch($this->rule);
    }

    /** @test */
    public function will_dispatch_correct_listeners_on_RuleDeleted()
    {
        $this->expectsListeners([])->forExpectedEvent(RuleDeleted::class);

        RuleDeleted::dispatch($this->rule);
    }

    /** @test */
    public function will_dispatch_correct_listeners_on_RecipientCreated()
    {
        $this->expectsListeners([
            CreatedRecipientSetHostTagMeta::class,
            SendRecipientEmailConfirmation::class,
            SendIntegromatInitMessage::class,
        ])->forExpectedEvent(RecipientCreated::class);

        RecipientCreated::dispatch($this->recipient);
    }

    /** @test */
    public function will_dispatch_correct_listeners_on_RecipientUpdated()
    {
        $this->expectsListeners([
            UpdatedRecipientSetHostTagMeta::class,
        ])->forExpectedEvent(RecipientUpdated::class);

        RecipientUpdated::dispatch($this->recipient);
    }

    /** @test */
    public function will_dispatch_correct_listeners_on_RecipientDeleted()
    {
        $this->expectsListeners([
            DeletedRecipientSetHostTagMeta::class,
        ])->forExpectedEvent(RecipientDeleted::class);

        RecipientDeleted::dispatch($this->recipient);
    }

    /** @test */
    public function will_dispatch_correct_listeners_on_RecipientVerified()
    {
        $this->expectsListeners([])->forExpectedEvent(RecipientVerified::class);

        RecipientVerified::dispatch($this->recipient);
    }

    /** @test */
    public function will_dispatch_correct_listeners_on_RecipientUnsubscribedFromDailySummary()
    {
        $this->expectsListeners([])->forExpectedEvent(RecipientUnsubscribedFromDailySummary::class);

        RecipientUnsubscribedFromDailySummary::dispatch($this->recipient);
    }

    /** @test */
    public function will_dispatch_correct_listeners_on_SupportMessageCreated()
    {
        $this->expectsListeners([
            SendSupportRequestConfirmation::class,
        ])->forExpectedEvent(SupportMessageCreated::class);

        SupportMessageCreated::dispatch($this->supportRequest);
    }

    /** @test */
    public function will_dispatch_correct_listeners_on_TeamMemberInvited()
    {
        $this->expectsListeners([
            SendTeamMemberInvitation::class,
        ])->forExpectedEvent(TeamMemberInvited::class);

        TeamMemberInvited::dispatch($this->teamMember, $this->user);
    }

    /** @test */
    public function will_dispatch_correct_listeners_on_TeamSettingsUpdated()
    {
        $this->expectsListeners([])->forExpectedEvent(TeamSettingsUpdated::class);

        TeamSettingsUpdated::dispatch($this->team, []);
    }

    /** @test */
    public function will_dispatch_correct_listeners_on_TeamPlanUpgraded()
    {
        $this->expectsListeners([])->forExpectedEvent(TeamPlanUpgraded::class);

        TeamPlanUpgraded::dispatch($this->team);
    }

    /** @test */
    public function will_dispatch_correct_listeners_on_TeamPlanDowngraded()
    {
        $this->expectsListeners([])->forExpectedEvent(TeamPlanDowngraded::class);

        TeamPlanDowngraded::dispatch($this->team);
    }

    /** @test */
    public function will_dispatch_correct_listeners_on_UserSettingsUpdated()
    {
        $this->expectsListeners([])->forExpectedEvent(UserSettingsUpdated::class);

        UserSettingsUpdated::dispatch($this->user, []);
    }

    /** @test */
    public function will_dispatch_correct_listeners_on_EventCreated()
    {
        $this->expectsListeners([])->forExpectedEvent(EventCreated::class);

        EventCreated::dispatch($this->event);
    }

    /** @test */
    public function will_dispatch_correct_listeners_on_EventUpdated()
    {
        $this->expectsListeners([])->forExpectedEvent(EventUpdated::class);

        EventUpdated::dispatch($this->event);
    }

    /** @test */
    public function will_dispatch_correct_listeners_on_EventDeleted()
    {
        $this->expectsListeners([
            RecoverDeletedEvent::class,
        ])->forExpectedEvent(EventDeleted::class);

        EventDeleted::dispatch($this->event);
    }

    /** @test */
    public function will_dispatch_correct_listeners_on_EventCommentCreated()
    {
        $this->expectsListeners([
            NotifyEventSubscribersOfNewComment::class,
        ])->forExpectedEvent(EventCommentCreated::class);

        EventCommentCreated::dispatch($this->eventComment);
    }

    /** @test */
    public function will_dispatch_correct_listeners_on_SettingUpdated()
    {
        $this->expectsListeners([
            LogSettingsActivity::class,
        ])->forExpectedEvent(SettingUpdated::class);

        SettingUpdated::dispatch(null, null, null, null);
    }

    /** @test */
    public function will_dispatch_correct_listeners_on_JobmonResultCreated()
    {
        $this->expectsListeners([])->forExpectedEvent(JobmonResultCreated::class);

        JobmonResultCreated::dispatch($this->jobmonResult);
    }

    /** @test */
    public function will_dispatch_correct_listeners_on_JobmonResultUpdated()
    {
        $this->expectsListeners([])->forExpectedEvent(JobmonResultUpdated::class);

        JobmonResultUpdated::dispatch($this->jobmonResult);
    }

    /** @test */
    public function will_dispatch_correct_listeners_on_JobmonResultDeleted()
    {
        $this->expectsListeners([])->forExpectedEvent(JobmonResultDeleted::class);

        JobmonResultDeleted::dispatch($this->jobmonResult);
    }

    protected function setUp(): void
    {
        parent::setUp();
        Event::fakeFor(function () {
            $this->user = User::factory()->create();
            $this->team = Team::factory()->create();
            $this->host = Host::factory()->create();
            $this->frontman = Frontman::factory()->create();
            $this->webCheck = WebCheck::factory()->create();
            $this->serviceCheck = ServiceCheck::factory()->create();
            $this->snmpCheck = SnmpCheck::factory()->create();
            $this->customCheck = CustomCheck::factory()->create();
            $this->rule = Rule::factory()->create();
            $this->recipient = Recipient::factory()->create();
            $this->supportRequest = SupportRequest::factory()->create();
            $this->teamMember = TeamMember::factory()->create();
            $this->event = \App\Models\Event::factory()->create();
            $this->eventComment = EventComment::factory()->create();
            $this->jobmonResult = JobmonResult::factory()->create();
        });
    }
}
