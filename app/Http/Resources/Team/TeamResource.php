<?php

namespace App\Http\Resources\Team;

use App\Http\Transformers\DateTransformer;
use App\Models\Host;
use App\Models\Team;
use App\Models\TeamMember;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Team
 */
class TeamResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                        => $this->id,
            'currency'                  => $this->currency,
            'dataRetention'             => $this->data_retention,
            'dateFormat'                => $this->date_format,
            'defaultFrontman'           => $this->default_frontman_id,
            'frontmanLocation'          => $this->defaultFrontman->location,
            'hostsHash'                 => Host::getHashOfAllTeamsHosts(),
            'maxFrontmen'               => $this->max_frontmen,
            'maxHosts'                  => $this->max_hosts,
            'maxMembers'                => $this->max_members,
            'maxRecipients'             => $this->max_recipients,
            'minCheckInterval'          => $this->min_check_interval,
            'name'                      => $this->name,
            'partner'                   => $this->partner,
            'partnerExtraData'          => $this->partner_extra_data,
            'plan'                      => $this->plan->value,
            'registrationTrack'         => $this->registration_track,
            'timezone'                  => $this->timezone,
            'trial'                     => $this->isOnTrial(),
            'trialRemainingDays'        => $this->when(
                $this->isOnTrial(),
                $this->trial_days_remaining
            ),
            'isNewTeam'                 => $this->is_new_team,
            'hasGrantedAccessToSupport' => $this->has_granted_access_to_support,
            'onboarded'                 => $this->onboarded,
            'settings'                  => team_settings($this->resource)->get(),
            'counts'                    => [
                'hosts'   => Host::count(),
                'members' => TeamMember::notDeleted()->notSupport()->count(),
                'admin'   => TeamMember::notDeleted()->activeAdmin()->notSupport()->count(),
            ],
            'dates'                     => [
                'trialEndsAt' => DateTransformer::transform($this->trial_ends_at),
                'updatedAt'   => DateTransformer::transform($this->updated_at),
                'createdAt'   => DateTransformer::transform($this->created_at),
            ],
        ];
    }
}
