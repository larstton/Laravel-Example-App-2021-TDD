<?php

namespace App\Models;

use App\Enums\RecipientMediaType;
use App\Events\Recipient\RecipientCreated;
use App\Events\Recipient\RecipientDeleted;
use App\Events\Recipient\RecipientUpdated;
use App\Models\Concerns\HasMeta;
use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\OwnedByTeam;
use App\Models\Concerns\PurgesCache;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

/**
 * @mixin IdeHelperRecipient
 */
class Recipient extends BaseModel
{
    use Notifiable, CastsEnums, OwnedByTeam, LogsActivity, PurgesCache, HasMeta;

    protected $dispatchesEvents = [
        'created' => RecipientCreated::class,
        'updated' => RecipientUpdated::class,
        'deleted' => RecipientDeleted::class,
    ];

    protected $enumCasts = [
        'media_type' => RecipientMediaType::class,
    ];

    protected $dates = [
        'verified_at',
    ];

    protected $casts = [
        'verified'                     => 'bool',
        'active'                       => 'bool',
        'permanent_failures_last_24_h' => 'int',
        'administratively_disabled'    => 'bool',
        'reminders'                    => 'bool',
        'daily_reports'                => 'bool',
        'monthly_reports'              => 'bool',
        'daily_summary'                => 'bool',
        'weekly_reports'               => 'bool',
        'comments'                     => 'bool',
        'alerts'                       => 'bool',
        'warnings'                     => 'bool',
        'event_uuids'                  => 'bool',
        'recoveries'                   => 'bool',
        'rules'                        => 'array',
    ];

    public function schemalessAttributeName()
    {
        return 'extra_data';
    }

    public function routeNotificationForMail()
    {
        return $this->sendto;
    }

    public function scopeActive(Builder $query)
    {
        return $query->where('active', true);
    }

    public function scopeVerified(Builder $query)
    {
        return $query->where('verified', true);
    }

    public function scopeWhereSubscribedToComments(Builder $query)
    {
        return $query->where('comments', true);
    }

    /**
     * @return BelongsTo|User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isVerified(): bool
    {
        return $this->verified;
    }

    public function purgeableEvents(): array
    {
        return [
            'created' => 'team-recipients-'.$this->team_id,
            'updated' => 'team-recipients-'.$this->team_id,
            'deleted' => 'team-recipients-'.$this->team_id,
        ];
    }

    protected function setActivityLogAction(string $eventName): string
    {
        return "Recipient {$this->sendto} ({$this->media_type}) {$eventName}";
    }
}
