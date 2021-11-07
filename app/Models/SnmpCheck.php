<?php

namespace App\Models;

use App\Casts\CheckLastSuccessStatusCast;
use App\Enums\CheckLastSuccess;
use App\Events\SnmpCheck\SnmpCheckCreated;
use App\Events\SnmpCheck\SnmpCheckDeleted;
use App\Events\SnmpCheck\SnmpCheckUpdated;
use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

/**
 * @mixin IdeHelperSnmpCheck
 */
class SnmpCheck extends BaseModel
{
    use LogsActivity;

    protected $dispatchesEvents = [
        'created' => SnmpCheckCreated::class,
        'updated' => SnmpCheckUpdated::class,
        'deleted' => SnmpCheckDeleted::class,
    ];

    protected $casts = [
        'active'          => 'bool',
        'check_interval'  => 'int',
        'last_success'    => CheckLastSuccessStatusCast::class,
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
     * Will get the Team which owns the snmp-check using the immediate
     * relationship of the host.
     *
     * SnmpCheck [belongsTo] Host [belongsTo] Team
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

    protected function setActivityLogAction(string $eventName): string
    {
        if ($eventName === 'updating') {
            $text = 'SNMP check updated ';
            foreach ($this->getAttributes() as $key => $value) {
                if (is_null($value) || $value === false) {
                    $value = (string) '0';
                }
                $text .= sprintf('%s=%s ', $key, $value);
            }

            return $text;
        }

        return sprintf('SNMP Check "%s" %s', $this->preset, $eventName);
    }
}
