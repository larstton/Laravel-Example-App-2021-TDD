<?php /** @noinspection ALL */

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models {

    /**
     * App\Models\ActivityLog
     *
     * @mixin IdeHelperActivityLog
     * @property int $id
     * @property string|null $team_id
     * @property string|null $log_name
     * @property string $description
     * @property string|null $subject_id
     * @property string|null $subject_type
     * @property string|null $causer_id
     * @property string|null $causer_type
     * @property \Illuminate\Support\Collection|null $properties
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $causer
     * @property-read \Illuminate\Support\Collection $changes
     * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $subject
     * @property-read \App\Models\Team|null $team
     * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog allTeams()
     * @method static Builder|Activity causedBy(\Illuminate\Database\Eloquent\Model $causer)
     * @method static Builder|Activity forSubject(\Illuminate\Database\Eloquent\Model $subject)
     * @method static Builder|Activity inLog($logNames)
     * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog query()
     * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereCauserId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereCauserType($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereLogName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereProperties($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereSubjectId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereSubjectType($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereTeamId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog withoutTeamScope()
     */
    class IdeHelperActivityLog extends \Eloquent
    {
    }
}

namespace App\Models {

    /**
     * App\Models\ApiToken
     *
     * @mixin IdeHelperApiToken
     * @property string $id
     * @property string $team_id
     * @property string $token
     * @property string $name
     * @property string $capability
     * @property string|null $last_usage_ip_address
     * @property \Illuminate\Support\Carbon|null $last_used_at
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ActivityLog[] $actions
     * @property-read int|null $actions_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ActivityLog[] $activities
     * @property-read int|null $activities_count
     * @property-read \App\Models\Team $team
     * @method static \Illuminate\Database\Eloquent\Builder|ApiToken allTeams()
     * @method static \Illuminate\Database\Eloquent\Builder|ApiToken newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|ApiToken newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|ApiToken query()
     * @method static \Illuminate\Database\Eloquent\Builder|ApiToken whereCapability($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ApiToken whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ApiToken whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ApiToken whereLastUsageIpAddress($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ApiToken whereLastUsedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ApiToken whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ApiToken whereTeamId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ApiToken whereToken($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ApiToken whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|BaseModel whereUuid($uuid, $uuidColumn = null)
     * @method static \Illuminate\Database\Eloquent\Builder|ApiToken withoutTeamScope()
     */
    class IdeHelperApiToken extends \Eloquent implements \App\Models\Concerns\AuthedEntity, \Illuminate\Contracts\Auth\Authenticatable
    {
    }
}

namespace App\Models {

    /**
     * App\Models\CheckResult
     *
     * @mixin IdeHelperCheckResult
     * @property int $id
     * @property string $check_id
     * @property string $host_id
     * @property string $check_type
     * @property array $data
     * @property int|null $success
     * @property string|null $frontman_id
     * @property string|null $user_agent
     * @property \Illuminate\Support\Carbon|null $data_updated_at
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property-read Model|\Eloquent $check
     * @property-read \App\Models\Host $host
     * @method static Builder|CheckResult hostChecksByType(\App\Models\Host $host, \App\Enums\CheckType $type)
     * @method static Builder|CheckResult newModelQuery()
     * @method static Builder|CheckResult newQuery()
     * @method static Builder|CheckResult query()
     * @method static Builder|CheckResult whereCheckId($value)
     * @method static Builder|CheckResult whereCheckIdMatchesChecksOfHost(\App\Models\Host $host)
     * @method static Builder|CheckResult whereCheckType($value)
     * @method static Builder|CheckResult whereCreatedAt($value)
     * @method static Builder|CheckResult whereData($value)
     * @method static Builder|CheckResult whereDataUpdatedAt($value)
     * @method static Builder|CheckResult whereFrontmanId($value)
     * @method static Builder|CheckResult whereHostId($value)
     * @method static Builder|CheckResult whereId($value)
     * @method static Builder|CheckResult whereSuccess($value)
     * @method static Builder|CheckResult whereUserAgent($value)
     */
    class IdeHelperCheckResult extends \Eloquent
    {
    }
}

namespace App\Models {

    /**
     * App\Models\CpuUtilisationSnapshot
     *
     * @mixin IdeHelperCpuUtilisationSnapshot
     * @property int $id
     * @property string $host_id
     * @property array $settings
     * @property array $top
     * @property \Illuminate\Support\Carbon|null $created_at
     * @method static \Illuminate\Database\Eloquent\Builder|CpuUtilisationSnapshot newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|CpuUtilisationSnapshot newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|CpuUtilisationSnapshot query()
     * @method static \Illuminate\Database\Eloquent\Builder|CpuUtilisationSnapshot whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CpuUtilisationSnapshot whereHostId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CpuUtilisationSnapshot whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CpuUtilisationSnapshot whereSettings($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CpuUtilisationSnapshot whereTop($value)
     */
    class IdeHelperCpuUtilisationSnapshot extends \Eloquent
    {
    }
}

namespace App\Models {

    /**
     * App\Models\CustomCheck
     *
     * @mixin IdeHelperCustomCheck
     * @property string $id
     * @property string $host_id
     * @property string $user_id
     * @property string $name
     * @property string $token
     * @property int|null $expected_update_interval
     * @property string|null $last_influx_error
     * @property \Illuminate\Support\Carbon|null $last_checked_at
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read int|null $last_success
     * @property-read \App\Models\Host $host
     * @property-read \App\Models\Team|null $teamOwner
     * @method static \Illuminate\Database\Eloquent\Builder|CustomCheck newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|CustomCheck newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|CustomCheck query()
     * @method static \Illuminate\Database\Eloquent\Builder|CustomCheck whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CustomCheck whereExpectedUpdateInterval($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CustomCheck whereHostId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CustomCheck whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CustomCheck whereLastCheckedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CustomCheck whereLastInfluxError($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CustomCheck whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CustomCheck whereToken($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CustomCheck whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CustomCheck whereUserId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|BaseModel whereUuid($uuid, $uuidColumn = null)
     */
    class IdeHelperCustomCheck extends \Eloquent
    {
    }
}

namespace App\Models {

    /**
     * App\Models\Event
     *
     * @mixin IdeHelperEvent
     * @property string $id
     * @property string $team_id
     * @property string|null $host_id
     * @property string $rule_id
     * @property string $check_id
     * @property string $check_key
     * @property string $action
     * @property \App\Enums\EventState|int $state
     * @property \App\Enums\EventReminder|int $reminders
     * @property \Spatie\SchemalessAttributes\SchemalessAttributes $meta
     * @property string|null $affected_host_id
     * @property int|null $is_agent_event
     * @property string|null $last_check_value
     * @property \Illuminate\Support\Carbon|null $last_checked_at
     * @property \Illuminate\Support\Carbon|null $resolved_at
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ActivityLog[] $activities
     * @property-read int|null $activities_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventComment[] $eventComments
     * @property-read int|null $event_comments_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventComment[] $guestOnlyComments
     * @property-read int|null $guest_only_comments_count
     * @property-read \App\Models\Host|null $host
     * @property-read \App\Models\Rule $rule
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Reminder[] $sentReminders
     * @property-read int|null $sent_reminders_count
     * @property-read \App\Models\Team $team
     * @method static Builder|Event allTeams()
     * @method static Builder|Event newModelQuery()
     * @method static Builder|Event newQuery()
     * @method static Builder|Event query()
     * @method static Builder|Event whereStartedOrResolvedBetweenDates(\Carbon\Carbon $from, \Carbon\Carbon $to)
     * @method static Builder|Event whereAction($value)
     * @method static \Illuminate\Database\Query\Builder|Event whereActive()
     * @method static Builder|Event whereActiveEventForHostAndJobId(\App\Models\Host $host, $jobId)
     * @method static Builder|Event whereAffectedHostId($value)
     * @method static Builder|Event whereAgentEvent()
     * @method static Builder|Event whereCheckId($value)
     * @method static Builder|Event whereCheckIdMatchesChecksOfHost(\App\Models\Host $host)
     * @method static Builder|Event whereCheckKey($value)
     * @method static Builder|Event whereCreatedAt($value)
     * @method static Builder|Event whereHostId($value)
     * @method static Builder|Event whereId($value)
     * @method static Builder|Event whereIsAgentEvent($value)
     * @method static Builder|Event whereLastCheckValue($value)
     * @method static Builder|Event whereLastCheckedAt($value)
     * @method static Builder|Event whereMeta($value)
     * @method static Builder|Event whereOnOrAfter(\Carbon\Carbon $carbon)
     * @method static Builder|Event whereReminders($value)
     * @method static Builder|Event whereResolvedAt($value)
     * @method static Builder|Event whereRuleId($value)
     * @method static Builder|Event whereState($value)
     * @method static Builder|Event whereTeamId($value)
     * @method static Builder|Event whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|BaseModel whereUuid($uuid, $uuidColumn = null)
     * @method static Builder|Event whereWarningOrAlert()
     * @method static Builder|Event withMeta()
     * @method static Builder|Event withoutTeamScope()
     */
    class IdeHelperEvent extends \Eloquent
    {
    }
}

namespace App\Models {

    /**
     * App\Models\EventComment
     *
     * @mixin IdeHelperEventComment
     * @property string $id
     * @property string $event_id
     * @property string $team_id
     * @property string $user_id
     * @property string|null $nickname
     * @property string $text
     * @property bool $visible_to_guests Is the comment visible for guests
     * @property bool $statuspage Publish comment on status pages
     * @property bool $forward Forward comment to subscribed email recipients.
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ActivityLog[] $activities
     * @property-read int|null $activities_count
     * @property-read \App\Models\Event $event
     * @property-read \App\Models\Team $team
     * @property-read \App\Models\User $user
     * @method static \Illuminate\Database\Eloquent\Builder|EventComment allTeams()
     * @method static \Illuminate\Database\Eloquent\Builder|EventComment newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|EventComment newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|EventComment query()
     * @method static \Illuminate\Database\Eloquent\Builder|EventComment whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EventComment whereEventId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EventComment whereForward($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EventComment whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EventComment whereNickname($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EventComment whereStatuspage($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EventComment whereTeamId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EventComment whereText($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EventComment whereUserId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|BaseModel whereUuid($uuid, $uuidColumn = null)
     * @method static \Illuminate\Database\Eloquent\Builder|EventComment whereVisibleToGuests($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EventComment withoutTeamScope()
     */
    class IdeHelperEventComment extends \Eloquent
    {
    }
}

namespace App\Models {

    /**
     * App\Models\Frontman
     *
     * @mixin IdeHelperFrontman
     * @property string $id
     * @property string $team_id
     * @property string|null $location
     * @property \Illuminate\Support\Carbon|null $last_heartbeat_at
     * @property string $password
     * @property string $user_id
     * @property mixed|null $host_info
     * @property string|null $host_info_last_updated_at
     * @property string|null $version
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read string $type
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Host[] $hosts
     * @property-read int|null $hosts_count
     * @property-read \App\Models\Team $team
     * @method static Builder|Frontman allTeams()
     * @method static Builder|Frontman newModelQuery()
     * @method static Builder|Frontman newQuery()
     * @method static Builder|Frontman private ()
     * @method static Builder|Frontman public ()
     * @method static Builder|Frontman query()
     * @method static Builder|Frontman whereCreatedAt($value)
     * @method static Builder|Frontman whereHostInfo($value)
     * @method static Builder|Frontman whereHostInfoLastUpdatedAt($value)
     * @method static Builder|Frontman whereId($value)
     * @method static Builder|Frontman whereLastHeartbeatAt($value)
     * @method static Builder|Frontman whereLocation($value)
     * @method static Builder|Frontman wherePassword($value)
     * @method static Builder|Frontman whereTeamId($value)
     * @method static Builder|Frontman whereUpdatedAt($value)
     * @method static Builder|Frontman whereUserId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|BaseModel whereUuid($uuid, $uuidColumn = null)
     * @method static Builder|Frontman whereVersion($value)
     * @method static Builder|Frontman withoutTeamScope()
     */
    class IdeHelperFrontman extends \Eloquent
    {
    }
}

namespace App\Models {

    /**
     * App\Models\Host
     *
     * @mixin IdeHelperHost
     * @property string $id
     * @property string $name
     * @property string $team_id
     * @property string $frontman_id
     * @property string|null $sub_unit_id
     * @property string|null $description
     * @property string $state
     * @property string $user_id
     * @property string|null $last_update_user_id
     * @property string $password
     * @property string|null $connect
     * @property \App\Enums\HostActiveState|bool $active
     * @property bool|null $cagent
     * @property Carbon|null $cagent_last_updated_at
     * @property Carbon|null $snmp_check_last_updated_at
     * @property Carbon|null $web_check_last_updated_at
     * @property Carbon|null $service_check_last_updated_at
     * @property Carbon|null $custom_check_last_updated_at
     * @property array|null $inventory
     * @property int|null $cagent_metrics
     * @property bool|null $dashboard
     * @property bool|null $muted
     * @property array|null $hw_inventory
     * @property string|null $snmp_protocol
     * @property int|null $snmp_port
     * @property string|null $snmp_community
     * @property int|null $snmp_timeout
     * @property string|null $snmp_privacy_protocol
     * @property string|null $snmp_security_level
     * @property string|null $snmp_authentication_protocol
     * @property string|null $snmp_username
     * @property string|null $snmp_authentication_password
     * @property string|null $snmp_privacy_password
     * @property Carbon|null $deleted_at
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ActivityLog[] $activities
     * @property-read int|null $activities_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CheckResult[] $checkResults
     * @property-read int|null $check_results_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CpuUtilisationSnapshot[] $cpuUtilisationSnapshots
     * @property-read int|null $cpu_utilisation_snapshots_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CustomCheck[] $customChecks
     * @property-read int|null $custom_checks_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Event[] $events
     * @property-read int|null $events_count
     * @property-read \App\Models\Frontman $frontman
     * @property-read \App\Models\HostEventSummary $event_summary
     * @property-read Carbon $latest_check_date
     * @property-read \App\Models\HostSummary $summary
     * @property-read int $total_checks_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\HostHistory[] $histories
     * @property-read int|null $histories_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\JobmonResult[] $jobmonResults
     * @property-read int|null $jobmon_results_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\JobmonResult[] $jobmonResultsGroupedByJobId
     * @property-read int|null $jobmon_results_grouped_by_job_id_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ServiceCheck[] $serviceChecks
     * @property-read int|null $service_checks_count
     * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Tag[] $tags
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SnmpCheck[] $snmpChecks
     * @property-read int|null $snmp_checks_count
     * @property-read \App\Models\SubUnit|null $subUnit
     * @property-read int|null $tags_count
     * @property-read \App\Models\Team $team
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\WebCheck[] $webChecks
     * @property-read int|null $web_checks_count
     * @method static Builder|Host active()
     * @method static Builder|Host allTeams()
     * @method static Builder|Host inactive()
     * @method static Builder|Host newModelQuery()
     * @method static Builder|Host newQuery()
     * @method static Builder|Host onlyDashboardVisible($value = true)
     * @method static QueryBuilder|Host onlyTrashed()
     * @method static Builder|Host query()
     * @method static Builder|Host whereActive($value)
     * @method static Builder|Host whereCagent($value)
     * @method static Builder|Host whereCagentLastUpdatedAt($value)
     * @method static Builder|Host whereCagentMetrics($value)
     * @method static Builder|Host whereConnect($value)
     * @method static Builder|Host whereCreatedAt($value)
     * @method static Builder|Host whereCustomCheckLastUpdatedAt($value)
     * @method static Builder|Host whereDashboard($value)
     * @method static Builder|Host whereDeletedAt($value)
     * @method static Builder|Host whereDescription($value)
     * @method static Builder|Host whereFrontmanId($value)
     * @method static Builder|Host whereHasActiveEvents($value = true)
     * @method static Builder|Host whereHasAllGroupTags($groups)
     * @method static Builder|Host whereHasAllTags($tags)
     * @method static Builder|Host whereHwInventory($value)
     * @method static Builder|Host whereId($value)
     * @method static Builder|Host whereInventory($value)
     * @method static Builder|Host whereLastUpdateUserId($value)
     * @method static Builder|Host whereMuted($value)
     * @method static Builder|Host whereName($value)
     * @method static Builder|Host wherePassword($value)
     * @method static Builder|Host whereScopedByUserHostTag(\App\Models\User $user = null)
     * @method static Builder|Host whereScopedByUserSubUnit(\App\Models\User $user = null)
     * @method static Builder|Host whereServiceCheckLastUpdatedAt($value)
     * @method static Builder|Host whereSnmpAuthenticationPassword($value)
     * @method static Builder|Host whereSnmpAuthenticationProtocol($value)
     * @method static Builder|Host whereSnmpCheckLastUpdatedAt($value)
     * @method static Builder|Host whereSnmpCommunity($value)
     * @method static Builder|Host whereSnmpPort($value)
     * @method static Builder|Host whereSnmpPrivacyPassword($value)
     * @method static Builder|Host whereSnmpPrivacyProtocol($value)
     * @method static Builder|Host whereSnmpProtocol($value)
     * @method static Builder|Host whereSnmpSecurityLevel($value)
     * @method static Builder|Host whereSnmpTimeout($value)
     * @method static Builder|Host whereSnmpUsername($value)
     * @method static Builder|Host whereState($value)
     * @method static Builder|Host whereSubUnitId($value)
     * @method static Builder|Host whereAllTagsBeginWithString($tags, $type = null)
     * @method static Builder|Host whereTagsV2($value)
     * @method static Builder|Host whereTeamId($value)
     * @method static Builder|Host whereUpdatedAt($value)
     * @method static Builder|Host whereUserId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|BaseModel whereUuid($uuid, $uuidColumn = null)
     * @method static Builder|Host whereWebCheckLastUpdatedAt($value)
     * @method static Builder|Host withActiveEventsConstrained($limitToActions = null, $limitCommentsToGuestVisible = false)
     * @method static Builder|Host withAgent()
     * @method static Builder|Host withAllTags($tags, $type = null)
     * @method static Builder|Host withAllTagsOfAnyType($tags)
     * @method static Builder|Host withAnyTags($tags, $type = null)
     * @method static Builder|Host withAnyTagsOfAnyType($tags)
     * @method static Builder|Host withCheckCount()
     * @method static Builder|Host withSnmpLastUpdatedAt()
     * @method static QueryBuilder|Host withTrashed()
     * @method static Builder|Host withoutTeamScope()
     * @method static QueryBuilder|Host withoutTrashed()
     */
    class IdeHelperHost extends \Eloquent
    {
    }
}

namespace App\Models {

    /**
     * App\Models\HostHistory
     *
     * @mixin IdeHelperHostHistory
     * @property int $id
     * @property string $host_id
     * @property string $team_id
     * @property string $user_id
     * @property string|null $name
     * @property bool|null $paid
     * @property Carbon|null $deleted_at
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property-read \App\Models\User $createdBy
     * @property-read \App\Models\Host $host
     * @property-read \App\Models\Team $team
     * @method static Builder|HostHistory allTeams()
     * @method static Builder|HostHistory newModelQuery()
     * @method static Builder|HostHistory newQuery()
     * @method static \Illuminate\Database\Query\Builder|HostHistory onlyTrashed()
     * @method static Builder|HostHistory query()
     * @method static Builder|HostHistory whereCreatedAt($value)
     * @method static Builder|HostHistory whereDeletedAt($value)
     * @method static Builder|HostHistory whereHostId($value)
     * @method static Builder|HostHistory whereId($value)
     * @method static Builder|HostHistory whereInGivenMonth($month)
     * @method static Builder|HostHistory whereCreatedOrDeletedBetweenDates(\Carbon\Carbon $from, \Carbon\Carbon $to)
     * @method static Builder|HostHistory whereIsPaid()
     * @method static Builder|HostHistory whereName($value)
     * @method static Builder|HostHistory whereNotPaid()
     * @method static Builder|HostHistory wherePaid($value)
     * @method static Builder|HostHistory whereTeamId($value)
     * @method static Builder|HostHistory whereUpdatedAt($value)
     * @method static Builder|HostHistory whereUserId($value)
     * @method static \Illuminate\Database\Query\Builder|HostHistory withTrashed()
     * @method static Builder|HostHistory withoutTeamScope()
     * @method static \Illuminate\Database\Query\Builder|HostHistory withoutTrashed()
     */
    class IdeHelperHostHistory extends \Eloquent
    {
    }
}

namespace App\Models {

    /**
     * App\Models\JobmonResult
     *
     * @mixin IdeHelperJobmonResult
     * @property int $id
     * @property string $host_id
     * @property string $job_id
     * @property array $data
     * @property string|null $next_run
     * @property Carbon|null $created_at
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ActivityLog[] $activities
     * @property-read int|null $activities_count
     * @method static Builder|JobmonResult newModelQuery()
     * @method static Builder|JobmonResult newQuery()
     * @method static Builder|JobmonResult query()
     * @method static Builder|JobmonResult whereBetweenDates($from, $to)
     * @method static Builder|JobmonResult whereCreatedAt($value)
     * @method static Builder|JobmonResult whereData($value)
     * @method static Builder|JobmonResult whereHostId($value)
     * @method static Builder|JobmonResult whereHostIdAndGroupedByJobIdWithCount(\App\Models\Host $host)
     * @method static Builder|JobmonResult whereId($value)
     * @method static Builder|JobmonResult whereJobId($value)
     * @method static Builder|JobmonResult whereNextRun($value)
     */
    class IdeHelperJobmonResult extends \Eloquent
    {
    }
}

namespace App\Models {

    /**
     * App\Models\PaidMessageLog
     *
     * @mixin IdeHelperPaidMessageLog
     * @property string $recipient_id
     * @property string $team_id
     * @property string|null $media_type
     * @property string $sendto
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property-read \App\Models\Recipient $recipient
     * @property-read \App\Models\Team $team
     * @method static \Illuminate\Database\Eloquent\Builder|PaidMessageLog allTeams()
     * @method static \Illuminate\Database\Eloquent\Builder|PaidMessageLog newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|PaidMessageLog newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|PaidMessageLog query()
     * @method static \Illuminate\Database\Eloquent\Builder|PaidMessageLog whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|PaidMessageLog whereMediaType($value)
     * @method static \Illuminate\Database\Eloquent\Builder|PaidMessageLog whereRecipientId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|PaidMessageLog whereSendto($value)
     * @method static \Illuminate\Database\Eloquent\Builder|PaidMessageLog whereTeamId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|BaseModel whereUuid($uuid, $uuidColumn = null)
     * @method static \Illuminate\Database\Eloquent\Builder|PaidMessageLog withoutTeamScope()
     */
    class IdeHelperPaidMessageLog extends \Eloquent
    {
    }
}

namespace App\Models {

    /**
     * App\Models\Recipient
     *
     * @mixin IdeHelperRecipient
     * @property string $id
     * @property string $team_id
     * @property string $user_id
     * @property bool $verified
     * @property bool $active
     * @property string|null $verification_token
     * @property int|null $permanent_failures_last_24_h
     * @property bool $administratively_disabled
     * @property string|null $media_type
     * @property string $sendto
     * @property string|null $description
     * @property string|null $option1
     * @property int $reminder_delay
     * @property int $maximum_reminders
     * @property bool $reminders
     * @property bool $daily_reports
     * @property bool $monthly_reports
     * @property bool $daily_summary
     * @property bool $weekly_reports
     * @property bool $comments
     * @property bool $alerts
     * @property bool $warnings
     * @property bool $event_uuids
     * @property bool $recoveries
     * @property array|null $rules
     * @property array|null $extra_data
     * @property \Illuminate\Support\Carbon|null $verified_at
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ActivityLog[] $activities
     * @property-read int|null $activities_count
     * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
     * @property-read int|null $notifications_count
     * @property-read \App\Models\Team $team
     * @method static Builder|Recipient active()
     * @method static Builder|Recipient allTeams()
     * @method static Builder|Recipient newModelQuery()
     * @method static Builder|Recipient newQuery()
     * @method static Builder|Recipient query()
     * @method static Builder|Recipient verified()
     * @method static Builder|Recipient whereActive($value)
     * @method static Builder|Recipient whereAdministrativelyDisabled($value)
     * @method static Builder|Recipient whereAlerts($value)
     * @method static Builder|Recipient whereComments($value)
     * @method static Builder|Recipient whereCreatedAt($value)
     * @method static Builder|Recipient whereDailyReports($value)
     * @method static Builder|Recipient whereDailySummary($value)
     * @method static Builder|Recipient whereDescription($value)
     * @method static Builder|Recipient whereEventUuids($value)
     * @method static Builder|Recipient whereExtraData($value)
     * @method static Builder|Recipient whereId($value)
     * @method static Builder|Recipient whereMaximumReminders($value)
     * @method static Builder|Recipient whereMediaType($value)
     * @method static Builder|Recipient whereMonthlyReports($value)
     * @method static Builder|Recipient whereOption1($value)
     * @method static Builder|Recipient wherePermanentFailuresLast24H($value)
     * @method static Builder|Recipient whereRecoveries($value)
     * @method static Builder|Recipient whereReminderDelay($value)
     * @method static Builder|Recipient whereReminders($value)
     * @method static Builder|Recipient whereRules($value)
     * @method static Builder|Recipient whereSendto($value)
     * @method static Builder|Recipient whereSubscribedToComments()
     * @method static Builder|Recipient whereTeamId($value)
     * @method static Builder|Recipient whereUpdatedAt($value)
     * @method static Builder|Recipient whereUserId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|BaseModel whereUuid($uuid, $uuidColumn = null)
     * @method static Builder|Recipient whereVerificationToken($value)
     * @method static Builder|Recipient whereVerified($value)
     * @method static Builder|Recipient whereVerifiedAt($value)
     * @method static Builder|Recipient whereWarnings($value)
     * @method static Builder|Recipient whereWeeklyReports($value)
     * @method static Builder|Recipient withoutTeamScope()
     */
    class IdeHelperRecipient extends \Eloquent
    {
    }
}

namespace App\Models {

    /**
     * App\Models\Reminder
     *
     * @mixin IdeHelperReminder
     * @property string $recipient_id
     * @property string $event_id
     * @property int $reminders_count
     * @property string|null $last_reminder_created_at
     * @property-read \App\Models\Event $event
     * @property-read \App\Models\Recipient $recipient
     * @method static \Illuminate\Database\Eloquent\Builder|Reminder newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Reminder newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Reminder query()
     * @method static \Illuminate\Database\Eloquent\Builder|Reminder whereEventId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Reminder whereLastReminderCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Reminder whereRecipientId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Reminder whereRemindersCount($value)
     * @method static \Illuminate\Database\Eloquent\Builder|BaseModel whereUuid($uuid, $uuidColumn = null)
     */
    class IdeHelperReminder extends \Eloquent
    {
    }
}

namespace App\Models {

    /**
     * App\Models\Rule
     *
     * @mixin IdeHelperRule
     * @property string $id
     * @property string $team_id
     * @property string $host_match_part
     * @property string $host_match_criteria
     * @property bool $finish
     * @property \App\Enums\Rule\RuleAction|string $action
     * @property int $position
     * @property string $check_key
     * @property array $check_type
     * @property \App\Enums\Rule\RuleFunction|string $function
     * @property \App\Enums\Rule\RuleOperator|string $operator
     * @property array|null $key_function
     * @property int $results_range
     * @property float $threshold
     * @property \App\Enums\Rule\RuleThresholdUnit|string|null $unit
     * @property string $user_id
     * @property bool $active
     * @property string|null $expression_alias
     * @property string $checksum
     * @property bool $mandatory
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ActivityLog[] $activities
     * @property-read int|null $activities_count
     * @property-read \App\Models\Team $team
     * @method static \Illuminate\Database\Eloquent\Builder|Rule allTeams()
     * @method static \Illuminate\Database\Eloquent\Builder|Rule newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Rule newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Rule ordered($direction = 'asc')
     * @method static \Illuminate\Database\Eloquent\Builder|Rule query()
     * @method static \Illuminate\Database\Eloquent\Builder|Rule whereAction($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Rule whereActive($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Rule whereCheckKey($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Rule whereCheckType($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Rule whereChecksum($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Rule whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Rule whereExpressionAlias($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Rule whereFinish($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Rule whereFunction($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Rule whereHostMatchCriteria($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Rule whereHostMatchPart($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Rule whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Rule whereKeyFunction($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Rule whereMandatory($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Rule whereOperator($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Rule wherePosition($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Rule whereResultsRange($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Rule whereTeamId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Rule whereThreshold($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Rule whereUnit($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Rule whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Rule whereUserId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|BaseModel whereUuid($uuid, $uuidColumn = null)
     * @method static \Illuminate\Database\Eloquent\Builder|Rule withoutTeamScope()
     */
    class IdeHelperRule extends \Eloquent implements \Spatie\EloquentSortable\Sortable
    {
    }
}

namespace App\Models {

    /**
     * App\Models\ServiceCheck
     *
     * @mixin IdeHelperServiceCheck
     * @property string $id
     * @property string $host_id
     * @property string $user_id
     * @property bool $active
     * @property int $check_interval
     * @property string $protocol
     * @property string $service
     * @property int $port
     * @property bool $in_progress
     * @property \App\Enums\CheckLastSuccess|bool|null $last_success
     * @property string|null $last_message
     * @property \Illuminate\Support\Carbon|null $last_checked_at
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ActivityLog[] $activities
     * @property-read int|null $activities_count
     * @property-read \App\Models\Host $host
     * @property-read \App\Models\Team|null $teamOwner
     * @method static \Illuminate\Database\Eloquent\Builder|ServiceCheck newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|ServiceCheck newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|ServiceCheck query()
     * @method static \Illuminate\Database\Eloquent\Builder|ServiceCheck whereActive($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ServiceCheck whereCheckInterval($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ServiceCheck whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ServiceCheck whereHostId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ServiceCheck whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ServiceCheck whereInProgress($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ServiceCheck whereLastCheckedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ServiceCheck whereLastMessage($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ServiceCheck whereLastSuccess($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ServiceCheck wherePort($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ServiceCheck whereProtocol($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ServiceCheck whereService($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ServiceCheck whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ServiceCheck whereUserId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|BaseModel whereUuid($uuid, $uuidColumn = null)
     */
    class IdeHelperServiceCheck extends \Eloquent
    {
    }
}

namespace App\Models {

    /**
     * App\Models\SnmpCheck
     *
     * @mixin IdeHelperSnmpCheck
     * @property string $id
     * @property string $host_id
     * @property string $user_id
     * @property bool $active
     * @property int $check_interval
     * @property string $preset
     * @property int|null $last_success
     * @property string|null $last_message
     * @property \Illuminate\Support\Carbon|null $last_checked_at
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ActivityLog[] $activities
     * @property-read int|null $activities_count
     * @property-read \App\Models\Host $host
     * @property-read \App\Models\Team|null $teamOwner
     * @method static \Illuminate\Database\Eloquent\Builder|SnmpCheck newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|SnmpCheck newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|SnmpCheck query()
     * @method static \Illuminate\Database\Eloquent\Builder|SnmpCheck whereActive($value)
     * @method static \Illuminate\Database\Eloquent\Builder|SnmpCheck whereCheckInterval($value)
     * @method static \Illuminate\Database\Eloquent\Builder|SnmpCheck whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|SnmpCheck whereHostId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|SnmpCheck whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|SnmpCheck whereLastCheckedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|SnmpCheck whereLastMessage($value)
     * @method static \Illuminate\Database\Eloquent\Builder|SnmpCheck whereLastSuccess($value)
     * @method static \Illuminate\Database\Eloquent\Builder|SnmpCheck wherePreset($value)
     * @method static \Illuminate\Database\Eloquent\Builder|SnmpCheck whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|SnmpCheck whereUserId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|BaseModel whereUuid($uuid, $uuidColumn = null)
     */
    class IdeHelperSnmpCheck extends \Eloquent
    {
    }
}

namespace App\Models {

    /**
     * App\Models\StatusPage
     *
     * @mixin IdeHelperStatusPage
     * @property string $id
     * @property string $team_id
     * @property string|null $token
     * @property string|null $title
     * @property \Spatie\SchemalessAttributes\SchemalessAttributes $meta
     * @property mixed|null $image
     * @property string|null $image_content_type
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ActivityLog[] $activities
     * @property-read int|null $activities_count
     * @property-read \App\Models\Team $team
     * @method static \Illuminate\Database\Eloquent\Builder|StatusPage allTeams()
     * @method static \Illuminate\Database\Eloquent\Builder|StatusPage newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|StatusPage newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|StatusPage query()
     * @method static \Illuminate\Database\Eloquent\Builder|StatusPage whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|StatusPage whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|StatusPage whereImage($value)
     * @method static \Illuminate\Database\Eloquent\Builder|StatusPage whereImageContentType($value)
     * @method static \Illuminate\Database\Eloquent\Builder|StatusPage whereMeta($value)
     * @method static \Illuminate\Database\Eloquent\Builder|StatusPage whereTeamId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|StatusPage whereTitle($value)
     * @method static \Illuminate\Database\Eloquent\Builder|StatusPage whereToken($value)
     * @method static \Illuminate\Database\Eloquent\Builder|StatusPage whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|BaseModel whereUuid($uuid, $uuidColumn = null)
     * @method static \Illuminate\Database\Eloquent\Builder|StatusPage withMeta()
     * @method static \Illuminate\Database\Eloquent\Builder|StatusPage withoutTeamScope()
     */
    class IdeHelperStatusPage extends \Eloquent
    {
    }
}

namespace App\Models {

    /**
     * App\Models\SubUnit
     *
     * @mixin IdeHelperSubUnit
     * @property string $id
     * @property string $team_id
     * @property string $short_id
     * @property string|null $name
     * @property string|null $information
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ActivityLog[] $activities
     * @property-read int|null $activities_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Host[] $hosts
     * @property-read int|null $hosts_count
     * @property-read \App\Models\Team $team
     * @method static \Illuminate\Database\Eloquent\Builder|SubUnit allTeams()
     * @method static \Illuminate\Database\Eloquent\Builder|SubUnit newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|SubUnit newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|SubUnit query()
     * @method static \Illuminate\Database\Eloquent\Builder|SubUnit whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|SubUnit whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|SubUnit whereInformation($value)
     * @method static \Illuminate\Database\Eloquent\Builder|SubUnit whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|SubUnit whereShortId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|SubUnit whereTeamId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|SubUnit whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|BaseModel whereUuid($uuid, $uuidColumn = null)
     * @method static \Illuminate\Database\Eloquent\Builder|SubUnit withoutTeamScope()
     */
    class IdeHelperSubUnit extends \Eloquent
    {
    }
}

namespace App\Models {

    /**
     * App\Models\SupportRequest
     *
     * @mixin IdeHelperSupportRequest
     * @property string $id
     * @property string $user_id
     * @property string $team_id
     * @property string $email
     * @property string $subject
     * @property string $body
     * @property \App\Enums\SupportRequestState|string $state
     * @property array|null $attachment
     * @property-read \App\Models\Team $team
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest query()
     * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest whereAttachment($value)
     * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest whereBody($value)
     * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest whereEmail($value)
     * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest whereState($value)
     * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest whereSubject($value)
     * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest whereUserId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|BaseModel whereUuid($uuid, $uuidColumn = null)
     */
    class IdeHelperSupportRequest extends \Eloquent
    {
    }
}

namespace App\Models {

    /**
     * App\Models\Tag
     *
     * @mixin IdeHelperTag
     * @property int $id
     * @property string $team_id
     * @property array $name
     * @property array $slug
     * @property string|null $type
     * @property int|null $order_column
     * @property \Spatie\SchemalessAttributes\SchemalessAttributes $meta
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read array $translations
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Host[] $hosts
     * @property-read int|null $hosts_count
     * @property-read \App\Models\Team $team
     * @method static Builder|Tag allTeams()
     * @method static Builder|Tag containing($name, $locale = null)
     * @method static Builder|Tag newModelQuery()
     * @method static Builder|Tag newQuery()
     * @method static Builder|Tag ordered($direction = 'asc')
     * @method static Builder|Tag query()
     * @method static Builder|Tag whereCreatedAt($value)
     * @method static Builder|Tag whereId($value)
     * @method static Builder|Tag whereMeta($value)
     * @method static Builder|Tag whereName($value)
     * @method static Builder|Tag whereOrderColumn($value)
     * @method static Builder|Tag whereSlug($value)
     * @method static Builder|Tag whereTagIs($name, $locale = null)
     * @method static Builder|Tag whereTagNameBeginsWith($name, $type = null, $locale = null)
     * @method static Builder|Tag whereTeamId($value)
     * @method static Builder|Tag whereType($value)
     * @method static Builder|Tag whereUpdatedAt($value)
     * @method static Builder|Tag withMeta()
     * @method static Builder|Tag withType($type = null)
     * @method static Builder|Tag withoutTeamScope()
     */
    class IdeHelperTag extends \Eloquent
    {
    }
}

namespace App\Models {

    use App\Enums\TeamPlan;

    /**
     * App\Models\Team
     *
     * @mixin IdeHelperTeam
     * @property string $id
     * @property string|null $name
     * @property int $max_hosts
     * @property int $max_members
     * @property int $max_recipients
     * @property string|null $default_frontman_id
     * @property int $max_frontmen
     * @property int $min_check_interval
     * @property int|null $data_retention
     * @property TeamPlan|string|null $plan
     * @property TeamPlan|string|null $previous_plan
     * @property string $timezone
     * @property string|null $currency
     * @property string|null $company_name
     * @property string|null $company_phone
     * @property mixed|null $registration_track
     * @property string|null $partner
     * @property mixed|null $partner_extra_data
     * @property string|null $date_format
     * @property bool|null $has_granted_access_to_support
     * @property bool|null $onboarded
     * @property bool|null $has_created_host
     * @property \Illuminate\Support\Carbon|null $plan_last_changed_at
     * @property \Illuminate\Support\Carbon|null $trial_ends_at
     * @property \Illuminate\Support\Carbon|null $upgraded_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ActivityLog[] $activityLogs
     * @property-read int|null $activity_logs_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $admins
     * @property-read int|null $admins_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ApiToken[] $apiTokens
     * @property-read int|null $api_tokens_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CheckResult[] $checkResults
     * @property-read int|null $check_results_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CustomCheck[] $customChecks
     * @property-read int|null $custom_checks_count
     * @property-read \App\Models\Frontman $defaultFrontman
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventComment[] $eventComments
     * @property-read int|null $event_comments_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Event[] $events
     * @property-read int|null $events_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Frontman[] $frontmen
     * @property-read int|null $frontmen_count
     * @property-read mixed $trial_days_remaining
     * @property-read bool $is_new_team
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\HostHistory[] $hostHistories
     * @property-read int|null $host_histories_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Host[] $hosts
     * @property-read int|null $hosts_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\JobmonResult[] $jobMonResults
     * @property-read int|null $job_mon_results_count
     * @property-read \App\Models\User $originalTeamMember
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PaidMessageLog[] $paidMessageLog
     * @property-read int|null $paid_message_log_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Recipient[] $recipients
     * @property-read int|null $recipients_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Reminder[] $reminders
     * @property-read int|null $reminders_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Rule[] $rules
     * @property-read int|null $rules_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ServiceCheck[] $serviceChecks
     * @property-read int|null $service_checks_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SnmpCheck[] $snmpChecks
     * @property-read int|null $snmp_checks_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\StatusPage[] $statusPages
     * @property-read int|null $status_pages_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SubUnit[] $subUnits
     * @property-read int|null $sub_units_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $teamMembers
     * @property-read int|null $team_members_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TeamSetting[] $teamSettings
     * @property-read int|null $team_settings_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserSetting[] $userSettings
     * @property-read int|null $user_settings_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\WebCheck[] $webChecks
     * @property-read int|null $web_checks_count
     * @method static Builder|Team newModelQuery()
     * @method static Builder|Team newQuery()
     * @method static \Illuminate\Database\Query\Builder|Team onlyTrashed()
     * @method static Builder|Team query()
     * @method static Builder|Team whereCompanyName($value)
     * @method static Builder|Team whereCompanyPhone($value)
     * @method static Builder|Team whereCreatedAt($value)
     * @method static Builder|Team whereCurrency($value)
     * @method static Builder|Team whereDataRetention($value)
     * @method static Builder|Team whereDateFormat($value)
     * @method static Builder|Team whereDefaultFrontmanId($value)
     * @method static Builder|Team whereDeletedAt($value)
     * @method static Builder|Team whereHasGrantedAccessToSupport($value)
     * @method static Builder|Team whereId($value)
     * @method static Builder|Team whereMaxFrontmen($value)
     * @method static Builder|Team whereMaxHosts($value)
     * @method static Builder|Team whereMaxMembers($value)
     * @method static Builder|Team whereMaxRecipients($value)
     * @method static Builder|Team whereMinCheckInterval($value)
     * @method static Builder|Team whereName($value)
     * @method static Builder|Team wherePartner($value)
     * @method static Builder|Team wherePartnerExtraData($value)
     * @method static Builder|Team wherePlan($value)
     * @method static Builder|Team wherePlanLastChangedAt($value)
     * @method static Builder|Team wherePreviousPlan($value)
     * @method static Builder|Team whereRegistrationTrack($value)
     * @method static Builder|Team whereTimezone($value)
     * @method static Builder|Team whereTrialEndsAt($value)
     * @method static Builder|Team whereUpdatedAt($value)
     * @method static Builder|Team whereUpgradedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|BaseModel whereUuid($uuid, $uuidColumn = null)
     * @method static Builder|Team withOriginalTeamMember()
     * @method static \Illuminate\Database\Query\Builder|Team withTrashed()
     * @method static \Illuminate\Database\Query\Builder|Team withoutTrashed()
     */
    class IdeHelperTeam extends \Eloquent implements \App\Support\Tenancy\Contracts\IsTenant
    {
    }
}

namespace App\Models {

    /**
     * App\Models\TeamMember
     *
     * @mixin IdeHelperTeamMember
     * @property string $id
     * @property string $email
     * @property string $password
     * @property \App\Enums\TeamMemberRole|string $role
     * @property \App\Enums\TeamStatus|string $team_status
     * @property int $terms_accepted
     * @property int $privacy_accepted
     * @property bool $product_news
     * @property string|null $nickname
     * @property string|null $name
     * @property string|null $host_tag
     * @property string|null $lang
     * @property string $team_id
     * @property string|null $sub_unit_id
     * @property string|null $notes
     * @property string|null $remember_token
     * @property \Illuminate\Support\Carbon|null $email_verified_at
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ActivityLog[] $actions
     * @property-read int|null $actions_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ActivityLog[] $activities
     * @property-read int|null $activities_count
     * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
     * @property-read int|null $notifications_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserSetting[] $settings
     * @property-read int|null $settings_count
     * @property-read \App\Models\SubUnit|null $subUnit
     * @property-read \App\Models\Team $team
     * @method static Builder|TeamMember activeAdmin()
     * @method static Builder|TeamMember allTeams()
     * @method static Builder|User forMarketingReminders()
     * @method static Builder|User fromActiveTeam()
     * @method static Builder|User fromTeamWithHosts()
     * @method static Builder|User fromTeamWithoutHosts()
     * @method static Builder|TeamMember newModelQuery()
     * @method static Builder|TeamMember newQuery()
     * @method static Builder|TeamMember notDeleted()
     * @method static Builder|User notVerified()
     * @method static Builder|User planEndsBetween($plan, \Carbon\CarbonInterface $start, $period)
     * @method static Builder|TeamMember query()
     * @method static Builder|User registeredInPeriod(\Carbon\CarbonInterface $start, $period)
     * @method static Builder|User regularUser()
     * @method static Builder|TeamMember status(\App\Enums\TeamStatus $status)
     * @method static Builder|User subscribedToProductNews()
     * @method static Builder|User supportUser()
     * @method static Builder|User unverifiedUsers($hours)
     * @method static Builder|User verified()
     * @method static Builder|User verifiedInPeriod(\Carbon\CarbonInterface $start, $period)
     * @method static Builder|TeamMember whereCreatedAt($value)
     * @method static Builder|TeamMember whereEmail($value)
     * @method static Builder|TeamMember whereEmailVerifiedAt($value)
     * @method static Builder|TeamMember whereHostTag($value)
     * @method static Builder|TeamMember whereId($value)
     * @method static Builder|TeamMember whereLang($value)
     * @method static Builder|TeamMember whereName($value)
     * @method static Builder|TeamMember whereNickname($value)
     * @method static Builder|TeamMember whereNotes($value)
     * @method static Builder|TeamMember wherePassword($value)
     * @method static Builder|TeamMember wherePrivacyAccepted($value)
     * @method static Builder|TeamMember whereProductNews($value)
     * @method static Builder|TeamMember whereRememberToken($value)
     * @method static Builder|TeamMember whereRole($value)
     * @method static Builder|TeamMember whereSubUnitId($value)
     * @method static Builder|TeamMember whereTeamId($value)
     * @method static Builder|TeamMember whereTeamStatus($value)
     * @method static Builder|TeamMember whereTermsAccepted($value)
     * @method static Builder|TeamMember whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|BaseModel whereUuid($uuid, $uuidColumn = null)
     * @method static Builder|User withCagentHostsCreatedWithoutChecks(\Carbon\CarbonInterface $start, $period)
     * @method static Builder|User withHostsCreatedWithoutChecks(\Carbon\CarbonInterface $start, $period)
     * @method static Builder|User withoutHosts()
     * @method static Builder|TeamMember withoutTeamScope()
     */
    class IdeHelperTeamMember extends \Eloquent
    {
    }
}

namespace App\Models {

    /**
     * App\Models\TeamSetting
     *
     * @mixin IdeHelperTeamSetting
     * @property int $team_id
     * @property array $value
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @method static \Illuminate\Database\Eloquent\Builder|TeamSetting newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|TeamSetting newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|TeamSetting query()
     * @method static \Illuminate\Database\Eloquent\Builder|TeamSetting whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|TeamSetting whereTeamId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|TeamSetting whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|TeamSetting whereValue($value)
     */
    class IdeHelperTeamSetting extends \Eloquent
    {
    }
}

namespace App\Models {

    /**
     * App\Models\User
     *
     * @mixin IdeHelperUser
     * @property string $id
     * @property string $email
     * @property string $password
     * @property string $role
     * @property \App\Enums\TeamStatus|string $team_status
     * @property int $terms_accepted
     * @property int $privacy_accepted
     * @property bool $product_news
     * @property string|null $nickname
     * @property string|null $name
     * @property string|null $host_tag
     * @property string|null $lang
     * @property string $team_id
     * @property string|null $sub_unit_id
     * @property string|null $notes
     * @property string|null $remember_token
     * @property Carbon|null $email_verified_at
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ActivityLog[] $actions
     * @property-read int|null $actions_count
     * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
     * @property-read int|null $notifications_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserSetting[] $settings
     * @property-read int|null $settings_count
     * @property-read \App\Models\SubUnit|null $subUnit
     * @property-read \App\Models\Team $team
     * @method static Builder|User forMarketingReminders()
     * @method static Builder|User fromActiveTeam()
     * @method static Builder|User fromTeamWithHosts()
     * @method static Builder|User fromTeamWithoutHosts()
     * @method static Builder|User newModelQuery()
     * @method static Builder|User newQuery()
     * @method static Builder|User notDeleted()
     * @method static Builder|User notVerified()
     * @method static Builder|User planEndsBetween($plan, \Carbon\CarbonInterface $start, $period)
     * @method static Builder|User query()
     * @method static Builder|User registeredInPeriod(\Carbon\CarbonInterface $start, $period)
     * @method static Builder|User regularUser()
     * @method static Builder|User subscribedToProductNews()
     * @method static Builder|User supportUser()
     * @method static Builder|User unverifiedUsers($hours)
     * @method static Builder|User verified()
     * @method static Builder|User verifiedInPeriod(\Carbon\CarbonInterface $start, $period)
     * @method static Builder|User whereCreatedAt($value)
     * @method static Builder|User whereEmail($value)
     * @method static Builder|User whereEmailVerifiedAt($value)
     * @method static Builder|User whereHostTag($value)
     * @method static Builder|User whereId($value)
     * @method static Builder|User whereLang($value)
     * @method static Builder|User whereName($value)
     * @method static Builder|User whereNickname($value)
     * @method static Builder|User whereNotes($value)
     * @method static Builder|User wherePassword($value)
     * @method static Builder|User wherePrivacyAccepted($value)
     * @method static Builder|User whereProductNews($value)
     * @method static Builder|User whereRememberToken($value)
     * @method static Builder|User whereRole($value)
     * @method static Builder|User whereSubUnitId($value)
     * @method static Builder|User whereTeamId($value)
     * @method static Builder|User whereTeamStatus($value)
     * @method static Builder|User whereTermsAccepted($value)
     * @method static Builder|User whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|BaseModel whereUuid($uuid, $uuidColumn = null)
     * @method static Builder|User withCagentHostsCreatedWithoutChecks(\Carbon\CarbonInterface $start, $period)
     * @method static Builder|User withHostsCreatedWithoutChecks(\Carbon\CarbonInterface $start, $period)
     * @method static Builder|User withoutHosts()
     */
    class IdeHelperUser extends \Eloquent implements \Tymon\JWTAuth\Contracts\JWTSubject, \App\Models\Concerns\AuthedEntity, \Illuminate\Contracts\Auth\MustVerifyEmail, \Illuminate\Contracts\Auth\Authenticatable, \Illuminate\Contracts\Auth\Access\Authorizable, \Illuminate\Contracts\Auth\CanResetPassword
    {
    }
}

namespace App\Models {

    /**
     * App\Models\UserSetting
     *
     * @mixin IdeHelperUserSetting
     * @property int $user_id
     * @property array $value
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @method static \Illuminate\Database\Eloquent\Builder|UserSetting newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|UserSetting newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|UserSetting query()
     * @method static \Illuminate\Database\Eloquent\Builder|UserSetting whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|UserSetting whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|UserSetting whereUserId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|UserSetting whereValue($value)
     */
    class IdeHelperUserSetting extends \Eloquent
    {
    }
}

namespace App\Models {

    /**
     * App\Models\WebCheck
     *
     * @mixin IdeHelperWebCheck
     * @property string $id
     * @property string $host_id
     * @property string $user_id
     * @property string $path
     * @property string $protocol
     * @property int|null $port
     * @property string|null $expected_pattern
     * @property string $expected_pattern_presence
     * @property int|null $expected_http_status
     * @property bool $search_html_source
     * @property int $time_out
     * @property bool $ignore_ssl_errors
     * @property int $check_interval
     * @property bool $dont_follow_redirects
     * @property string $method
     * @property bool $active
     * @property int $in_progress
     * @property int|null $last_success
     * @property string|null $last_message
     * @property string|null $post_data
     * @property array|null $headers
     * @property string|null $headers_md5_sum
     * @property \Illuminate\Support\Carbon|null $last_checked_at
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ActivityLog[] $activities
     * @property-read int|null $activities_count
     * @property-read \App\Models\Host $host
     * @property-read \App\Models\Team|null $teamOwner
     * @method static \Illuminate\Database\Eloquent\Builder|WebCheck newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|WebCheck newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|WebCheck query()
     * @method static \Illuminate\Database\Eloquent\Builder|WebCheck whereActive($value)
     * @method static \Illuminate\Database\Eloquent\Builder|WebCheck whereCheckInterval($value)
     * @method static \Illuminate\Database\Eloquent\Builder|WebCheck whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|WebCheck whereDontFollowRedirects($value)
     * @method static \Illuminate\Database\Eloquent\Builder|WebCheck whereExpectedHttpStatus($value)
     * @method static \Illuminate\Database\Eloquent\Builder|WebCheck whereExpectedPattern($value)
     * @method static \Illuminate\Database\Eloquent\Builder|WebCheck whereExpectedPatternPresence($value)
     * @method static \Illuminate\Database\Eloquent\Builder|WebCheck whereHeaders($value)
     * @method static \Illuminate\Database\Eloquent\Builder|WebCheck whereHeadersMd5Sum($value)
     * @method static \Illuminate\Database\Eloquent\Builder|WebCheck whereHostId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|WebCheck whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|WebCheck whereIgnoreSslErrors($value)
     * @method static \Illuminate\Database\Eloquent\Builder|WebCheck whereInProgress($value)
     * @method static \Illuminate\Database\Eloquent\Builder|WebCheck whereLastCheckedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|WebCheck whereLastMessage($value)
     * @method static \Illuminate\Database\Eloquent\Builder|WebCheck whereLastSuccess($value)
     * @method static \Illuminate\Database\Eloquent\Builder|WebCheck whereMethod($value)
     * @method static \Illuminate\Database\Eloquent\Builder|WebCheck wherePath($value)
     * @method static \Illuminate\Database\Eloquent\Builder|WebCheck wherePort($value)
     * @method static \Illuminate\Database\Eloquent\Builder|WebCheck wherePostData($value)
     * @method static \Illuminate\Database\Eloquent\Builder|WebCheck whereProtocol($value)
     * @method static \Illuminate\Database\Eloquent\Builder|WebCheck whereSearchHtmlSource($value)
     * @method static \Illuminate\Database\Eloquent\Builder|WebCheck whereTimeOut($value)
     * @method static \Illuminate\Database\Eloquent\Builder|WebCheck whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|WebCheck whereUserId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|BaseModel whereUuid($uuid, $uuidColumn = null)
     */
    class IdeHelperWebCheck extends \Eloquent
    {
    }
}

