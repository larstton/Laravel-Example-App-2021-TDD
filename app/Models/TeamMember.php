<?php

namespace App\Models;

use App\Enums\TeamMemberRole;
use App\Enums\TeamStatus;
use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\OwnedByTeam;
use Illuminate\Database\Eloquent\Builder;

/**
 * @mixin IdeHelperTeamMember
 */
class TeamMember extends User
{
    use OwnedByTeam, LogsActivity;

    public $table = 'users';

    public function scopeActiveAdmin(Builder $query)
    {
        $query
            ->role(TeamMemberRole::Admin())
            ->status(TeamStatus::Joined());
    }

    public function scopeStatus(Builder $query, TeamStatus $status)
    {
        $query->where('team_status', $status);
    }

    public function scopeRole(Builder $query, TeamMemberRole $role)
    {
        $query->where('role', $role);
    }

    public function scopeNotSupport(Builder $query)
    {
        $query->where('email', 'not regexp', 'support\+[0-9]+@cloudradar.co');
    }

    public function makeVerificationSignature()
    {
        return sha1($this->email.config('cloudradar.salt').$this->id);
    }

    protected function setActivityLogAction(string $eventName): string
    {
        if (is_cloud_radar_support_email($this->email)) {
            return "Support user with email '{$this->email}' {$eventName} on your team.";
        }

        return "Account with email '{$this->email}' was {$eventName} on your team.";
    }
}
