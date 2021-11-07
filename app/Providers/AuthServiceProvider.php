<?php

namespace App\Providers;

use App\Extensions\ApiTokenGuard;
use App\Extensions\ApiTokenProvider;
use App\Models\ApiToken;
use App\Models\CustomCheck;
use App\Models\Event;
use App\Models\EventComment;
use App\Models\Frontman;
use App\Models\Host;
use App\Models\HostHistory;
use App\Models\Recipient;
use App\Models\Rule;
use App\Models\ServiceCheck;
use App\Models\SnmpCheck;
use App\Models\StatusPage;
use App\Models\SubUnit;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use App\Models\WebCheck;
use App\Policies\ApiTokenPolicy;
use App\Policies\CustomCheckPolicy;
use App\Policies\EventCommentPolicy;
use App\Policies\EventPolicy;
use App\Policies\FrontmanPolicy;
use App\Policies\HostHistoryPolicy;
use App\Policies\HostPolicy;
use App\Policies\JobmonResultPolicy;
use App\Policies\RecipientPolicy;
use App\Policies\RulePolicy;
use App\Policies\ServiceCheckPolicy;
use App\Policies\SnmpCheckPolicy;
use App\Policies\StatusPagePolicy;
use App\Policies\SubUnitPolicy;
use App\Policies\TeamMemberPolicy;
use App\Policies\TeamPolicy;
use App\Policies\WebCheckPolicy;
use Illuminate\Auth\Access\Response;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Host::class         => HostPolicy::class,
        Team::class         => TeamPolicy::class,
        Recipient::class    => RecipientPolicy::class,
        WebCheck::class     => WebCheckPolicy::class,
        ServiceCheck::class => ServiceCheckPolicy::class,
        SnmpCheck::class    => SnmpCheckPolicy::class,
        CustomCheck::class  => CustomCheckPolicy::class,
        Rule::class         => RulePolicy::class,
        TeamMember::class   => TeamMemberPolicy::class,
        SubUnit::class      => SubUnitPolicy::class,
        ApiToken::class     => ApiTokenPolicy::class,
        Frontman::class     => FrontmanPolicy::class,
        Event::class        => EventPolicy::class,
        EventComment::class => EventCommentPolicy::class,
        StatusPage::class   => StatusPagePolicy::class,
        HostHistory::class  => HostHistoryPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Auth::provider('api-token', fn () => new ApiTokenProvider);

        /*
         * Limit access to team admins only
         */
        Gate::define('role-team-admin', function (User $user) {
            return $user->isTeamAdmin()
                ? Response::allow()
                : Response::deny('You must be a team administrator.');
        });

        /*
        * Limit access to full team members but keep guests out
        */
        Gate::define('role-team-member', function (User $user) {
            return $user->isTeamMember()
                ? Response::allow()
                : Response::deny('You must be a full team member.');
        });
    }
}
