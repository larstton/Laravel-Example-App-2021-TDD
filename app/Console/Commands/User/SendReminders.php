<?php

namespace App\Console\Commands\User;

use App\Actions\Team\DeleteTeamAction;
use App\Models\User;
use App\Notifications\Auth\NewUserEmailVerificationNotification;
use App\Notifications\Team\AddCheckReminderNotification;
use App\Notifications\Team\CreateHostReminderNotification;
use App\Notifications\Team\InstallCagentReminderNotification;
use App\Notifications\Team\InstallFrontmanReminderNotification;
use App\Support\Tenancy\Facades\TenantManager;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;

class SendReminders extends Command
{
    protected $signature = 'cloudradar:user:reminders';

    protected $description = 'Sends reminders for user not verified account, user not added host, user not created any rules';

    public function handle()
    {
        TenantManager::disableTenancyChecks();

        collect([
            'send3HourVerificationReminder',
            'send24HourVerificationReminder',
            'send48HourVerificationReminder',
            'sendCreateHost1HourReminder',
            'sendCreateHost24HoursReminder',
            'sendAddCheckAfter1HourReminder',
            'sendAddCheckAfter24HoursReminder',
            'sendInstallCagent1HourReminder',
            'sendInstallFrontman1HourReminder',
            'sendInstallFrontman24HoursReminder',
        ])->each(function ($method) {
            try {
                app()->call([$this, $method]);
            } catch (Exception $exception) {
                logger()->error("Reminders {$method}. ".$exception->getMessage());
            }
        });
    }

    public function send3HourVerificationReminder()
    {
        User::unverifiedUsers(3)->get()
            ->each(
                function (User $user) {
                    $user->notify(
                        (new NewUserEmailVerificationNotification('reminder'))
                            ->locale($user->lang)
                    );
                }
            );
    }

    public function send24HourVerificationReminder()
    {
        User::unverifiedUsers(24)->get()
            ->each(
                function (User $user) {
                    $user->notify(
                        (new NewUserEmailVerificationNotification('reminder24h'))
                            ->locale($user->lang)
                    );
                }
            );
    }

    public function send48HourVerificationReminder()
    {
        User::unverifiedUsers(48)->get()
            ->each(
                function (User $user) {
                    $user->notify(
                        (new NewUserEmailVerificationNotification('reminder48h'))
                            ->locale($user->lang)
                    );
                }
            );
    }

    public function remove7DaysUnverifiedAccounts(DeleteTeamAction $deleteTeamAction): void
    {
        User::unverifiedUsers(24 * 7)
            ->each(function (User $user) use ($deleteTeamAction) {
                $deleteTeamAction->execute($user->team);
            });
    }

    public function sendCreateHost1HourReminder(): void
    {
        //create first host 1 hour
        User::verifiedInPeriod(now()->subHour(), Carbon::SECONDS_PER_MINUTE * Carbon::MINUTES_PER_HOUR)
            ->withoutHosts()
            ->notDeleted()
            ->fromActiveTeam()->get()
            ->each(function (User $user) {
                $user->notify((new CreateHostReminderNotification)->locale($user->lang));
            });
    }

    public function sendCreateHost24HoursReminder(): void
    {
        //create first host 24 hours
        User::verifiedInPeriod(now()->subDay(), Carbon::SECONDS_PER_MINUTE * Carbon::MINUTES_PER_HOUR)
            ->withoutHosts()
            ->notDeleted()
            ->fromActiveTeam()->get()
            ->each(function (User $user) {
                $user->notify((new CreateHostReminderNotification)->locale($user->lang));
            });
    }

    public function removeAccountWithoutHostsAfter7Days(DeleteTeamAction $deleteTeamAction): void
    {
        //If the account has been verified but no hosts are created we delete it after 7 days
        User::verifiedInPeriod(
            now()->subDays(7),
            Carbon::SECONDS_PER_MINUTE * Carbon::MINUTES_PER_HOUR
        )
            ->withoutHosts()
            ->notDeleted()
            ->fromActiveTeam()
            ->each(function (User $user) use ($deleteTeamAction) {
                $deleteTeamAction->execute($user->team);
            });
    }

    public function sendAddCheckAfter1HourReminder(): void
    {
        // add check 1 hour
        User::with(['team', 'team.hosts'])
            ->regularUser()
            ->withHostsCreatedWithoutChecks(
                now()->subHour(),
                Carbon::SECONDS_PER_MINUTE * Carbon::MINUTES_PER_HOUR
            )->get()
            ->each(function (User $user) {
                $user->notify((new AddCheckReminderNotification(Arr::first($user->team->hosts)))->locale($user->lang));
            });
    }

    public function sendAddCheckAfter24HoursReminder(): void
    {
        // add check 24 hours
        User::with(['team', 'team.hosts'])
            ->regularUser()
            ->withHostsCreatedWithoutChecks(
                now()->subHours(24),
                Carbon::SECONDS_PER_MINUTE * Carbon::MINUTES_PER_HOUR
            )->get()
            ->each(function (User $user) {
                $user->notify((new AddCheckReminderNotification)->locale($user->lang));
            });
    }

    public function sendInstallCagent1HourReminder(): void
    {
        //install cagent 1 hour
        User::with(['team', 'team.hosts'])
            ->regularUser()
            ->withCagentHostsCreatedWithoutChecks(
                now()->subHour(),
                Carbon::SECONDS_PER_MINUTE * Carbon::MINUTES_PER_HOUR
            )->get()
            ->each(function (User $user) {
                $user->notify((new InstallCagentReminderNotification(Arr::first($user->team->hosts)))->locale($user->lang));
            });
    }

    public function sendInstallFrontman1HourReminder(): void
    {
        //install frontman 1 hour
        User::with(['team', 'team.frontmen'])
            ->regularUser()
            ->withFrontmanHostsWithoutChecks(
                now()->subHour(),
                Carbon::SECONDS_PER_MINUTE * Carbon::MINUTES_PER_HOUR
            )->get()
            ->each(function (User $user) {
                $user->notify((new InstallFrontmanReminderNotification(Arr::first($user->team->frontmen)))->locale($user->lang));
            });
    }

    public function sendInstallFrontman24HoursReminder(): void
    {
        //install frontman 24 hours
        User::with(['team', 'team.frontmen'])
            ->regularUser()
            ->withFrontmanHostsWithoutChecks(
                now()->subHours(24),
                Carbon::SECONDS_PER_MINUTE * Carbon::MINUTES_PER_HOUR
            )->get()
            ->each(function (User $user) {
                $user->notify((new InstallFrontmanReminderNotification(Arr::first($user->team->frontmen)))->locale($user->lang));
            });
    }
}
