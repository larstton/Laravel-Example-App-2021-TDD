<?php

namespace App\Providers;

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
use App\Events\Recipient\RecipientAdministrativelyDisabled;
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
use CloudRadar\LaravelSettings\Event\SettingUpdated;
use Illuminate\Auth\Events\Verified as UserVerifiedEmail;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [

        /* AUTH EVENTS */
        NewUserRegistered::class                     => [
            SendEmailVerificationNotification::class,
            LogAgentData::class,
        ],
        UserVerifiedEmail::class                     => [
            ActivateRecipientOnEmailVerification::class,
        ],
        NewTeamCreated::class                        => [],
        UserLoggedIn::class                          => [
            RecordLoginEventToTeamActivity::class,
        ],
        UserLoggedOut::class                         => [],

        /*USER EVENTS*/
        UserUpdated::class                           => [],
        UserUpdatedPassword::class                   => [],

        /* HOST EVENTS */
        HostCreated::class                           => [
            AddNewHostToHistoryLog::class,
            AddNewHostToNotifierService::class,
        ],
        HostUpdated::class                           => [
            UpdateHostInHistoryLog::class,
            UpdateHostOnNotifierService::class,
        ],
        // Note: This will fire when Host is soft deleted and again when hard deleted. You can check
        // what's happening with `isForceDeleting()` on the Host, or checking in the DB manually.
        HostDeleted::class                           => [],

        /* FRONTMAN EVENTS */
        FrontmanCreated::class                       => [],
        FrontmanUpdated::class                       => [],
        FrontmanDeleted::class                       => [],

        /* WEB CHECK EVENTS */
        WebCheckCreated::class                       => [],
        WebCheckUpdated::class                       => [],
        WebCheckDeleted::class                       => [],

        /* SERVICE CHECK EVENTS */
        ServiceCheckCreated::class                   => [],
        ServiceCheckUpdated::class                   => [],
        ServiceCheckDeleted::class                   => [],

        /* SNMP CHECK EVENTS */
        SnmpCheckCreated::class                      => [],
        SnmpCheckUpdated::class                      => [],
        SnmpCheckDeleted::class                      => [],

        /* CUSTOM CHECK EVENTS */
        CustomCheckCreated::class                    => [],
        CustomCheckUpdated::class                    => [],
        CustomCheckDeleted::class                    => [],

        /* RULE EVENTS */
        RuleCreated::class                           => [],
        RuleUpdated::class                           => [],
        RuleDeleted::class                           => [],

        /* RECIPIENT EVENTS */
        RecipientCreated::class                      => [
            CreatedRecipientSetHostTagMeta::class,
            SendRecipientEmailConfirmation::class,
            SendIntegromatInitMessage::class,
        ],
        RecipientUpdated::class                      => [
            UpdatedRecipientSetHostTagMeta::class,
        ],
        RecipientDeleted::class                      => [
            DeletedRecipientSetHostTagMeta::class,
        ],
        RecipientVerified::class                     => [],
        RecipientUnsubscribedFromDailySummary::class => [],
        RecipientAdministrativelyDisabled::class     => [],

        /* SUPPORT EVENTS */
        SupportMessageCreated::class                 => [
            SendSupportRequestConfirmation::class,
        ],

        /* TEAM EVENTS */
        TeamMemberInvited::class                     => [
            SendTeamMemberInvitation::class,
        ],
        TeamSettingsUpdated::class                   => [],
        TeamPlanUpgraded::class                      => [],
        TeamPlanDowngraded::class                    => [],

        /* USER EVENTS */
        UserSettingsUpdated::class                   => [],

        /* EVENT EVENTS*/
        EventCreated::class                          => [],
        EventUpdated::class                          => [],
        EventDeleted::class                          => [
            RecoverDeletedEvent::class,
        ],

        /* EVENT COMMENTS */
        EventCommentCreated::class                   => [
            NotifyEventSubscribersOfNewComment::class,
        ],

        /* SETTINGS EVENTS */
        SettingUpdated::class                        => [
            LogSettingsActivity::class,
        ],

        /* JOBMON_RESULT EVENTS */
        JobmonResultCreated::class                   => [],
        JobmonResultUpdated::class                   => [],
        JobmonResultDeleted::class                   => [],


    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {

        //
    }
}
