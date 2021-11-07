<?php

namespace App\Models;

use App\Casts\CheckLastSuccessStatusCast;
use App\Enums\CheckLastSuccess;
use App\Events\WebCheck\WebCheckCreated;
use App\Events\WebCheck\WebCheckDeleted;
use App\Events\WebCheck\WebCheckUpdated;
use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\PurgesCache;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

/**
 * @mixin IdeHelperWebCheck
 */
class WebCheck extends BaseModel
{
    use LogsActivity, PurgesCache;

    protected $dispatchesEvents = [
        'created' => WebCheckCreated::class,
        'updated' => WebCheckUpdated::class,
        'deleted' => WebCheckDeleted::class,
    ];

    protected $casts = [
        'dont_follow_redirects' => 'bool',
        'ignore_ssl_errors'     => 'bool',
        'search_html_source'    => 'bool',
        'active'                => 'bool',
        'port'                  => 'int',
        'expected_http_status'  => 'int',
        'time_out'              => 'int',
        'check_interval'        => 'int',
        'last_success'          => CheckLastSuccessStatusCast::class,
        'in_progress'           => 'int',
        'headers'               => 'array',
        'last_checked_at'       => 'datetime',
    ];

    protected $attributes = [
        'last_success' => CheckLastSuccess::Pending,
    ];

    /**
     * Will get the Team which owns the web-check using the immediate
     * relationship of the host.
     *
     * WebCheck [belongsTo] Host [belongsTo] Team
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

    public function purgeableEvents(): array
    {
        return [
            'created' => 'webCheck-'.$this->id,
            'updated' => 'webCheck-'.$this->id,
            'deleted' => 'webCheck-'.$this->id,
        ];
    }

    protected function setActivityLogAction(string $eventName): string
    {
        $text = sprintf(
            'Web check %s %s://%s:%s%s %s',
            $this->method,
            $this->protocol,
            optional($this->host()->withTrashed()->first())->connect ?? '(deleted)',
            $this->port,
            $this->path,
            $eventName
        );

        return $text.' '.trim($this->appendActive().' '.$this->appendCheckInterval());
    }

    /**
     * @return BelongsTo|Host
     */
    public function host()
    {
        return $this->belongsTo(Host::class);
    }

    /**
     * @return BelongsTo|User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
