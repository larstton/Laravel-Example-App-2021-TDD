<?php

namespace App\Models;

use App\Casts\CheckLastSuccessStatusCast;
use App\Enums\CheckLastSuccess;
use App\Events\ServiceCheck\ServiceCheckCreated;
use App\Events\ServiceCheck\ServiceCheckDeleted;
use App\Events\ServiceCheck\ServiceCheckUpdated;
use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Support\Str;

/**
 * @mixin IdeHelperServiceCheck
 */
class ServiceCheck extends BaseModel
{
    use LogsActivity;

    protected $dispatchesEvents = [
        'created' => ServiceCheckCreated::class,
        'updated' => ServiceCheckUpdated::class,
        'deleted' => ServiceCheckDeleted::class,
    ];

    protected $casts = [
        'active'          => 'bool',
        'port'            => 'int',
        'check_interval'  => 'int',
        'last_success'    => CheckLastSuccessStatusCast::class,
        'in_progress'     => 'bool',
        'last_checked_at' => 'datetime',
    ];

    protected $attributes = [
        'last_success' => CheckLastSuccess::Pending,
    ];

    /**
     * @return BelongsTo|Host
     */
    public function host()
    {
        return $this->belongsTo(Host::class);
    }

    /**
     * Will get the Team which owns the service-check using the immediate
     * relationship of the host.
     *
     * ServiceCheck [belongsTo] Host [belongsTo] Team
     *
     * @return HasOneThrough|Team
     */
    public function teamOwner()
    {
        return $this->hasOneThrough(
            Team::class,
            Host::class,
            'id', // Foreign key on hosts table...
            'id', // Foreign key on teams table...
            'host_id', // Local key on webchecks table...
            'team_id' // Local key on hosts table...
        );
    }

    public function isIcmpCheck(): bool
    {
        return Str::lower($this->protocol) === 'icmp';
    }

    protected function setActivityLogAction(string $eventName): string
    {
        if ($eventName === 'updating') {
            $text = 'Service check updated ';
            foreach ($this->getAttributes() as $key => $value) {
                if (is_null($value) || $value === false) {
                    $value = (string) '0';
                }
                $text .= sprintf('%s=%s ', $key, $value);
            }

            return $text;
        }

        if ($this->port) {
            $text = sprintf(
                'Service check %s (%s) %s',
                $this->service,
                $this->port,
                $eventName
            );
        } else {
            $text = sprintf(
                'Service check %s %s',
                $this->service,
                $eventName
            );
        }

        return $text.' '.trim($this->appendActive().' '.$this->appendCheckInterval());
    }
}
