<?php

namespace App\Support;

use App\Data\Event\NotifyOnEventCommentData;
use App\Models\CustomCheck;
use App\Models\Event;
use App\Models\Host;
use App\Models\Recipient;
use App\Models\ServiceCheck;
use App\Models\SnmpCheck;
use App\Models\Team;
use App\Models\WebCheck;
use BenSampo\Enum\Enum;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class NotifierService
{
    private $config;
    private $response;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function addHost(Host $host): bool
    {
        return $this->post('hosts', [
            'uuid'        => $host->id,
            'teamUuid'    => $host->team_id,
            'name'        => $host->name,
            'connect'     => $host->connect,
            // Notifier v2 requires description to be string.
            'description' => $host->description ?? '',
        ]);
    }

    private function post($endpoint, $jsonPayload = [], $queryPayload = []): bool
    {
        return $this->send('post', $endpoint, $jsonPayload, $queryPayload);
    }

    private function send($method, $endpoint, $jsonPayload = [], $queryPayload = []): bool
    {
        $payload = collect($jsonPayload)->map(function ($item) {
            if (is_a($item, Enum::class)) {
                return $item->value;
            }

            return $item;
        });
        $endpoint .= rtrim('?'.http_build_query($queryPayload), '?&');

        try {
            logger('Sending data', [
                'data'     => $payload->all(),
                'asJson'   => $payload->toJson(),
                'config'   => $this->config,
                'endpoint' => $endpoint,
            ]);
            $this->response = Http::timeout($this->config['timeout'])
                ->retry(3, 100)
                ->baseUrl($this->config['base_url'])
                ->withBasicAuth($this->config['username'], $this->config['password'])
                ->withHeaders($this->config['headers'])
                ->withoutVerifying()
                ->withBody($payload->toJson(JSON_FORCE_OBJECT), 'application/json')
                ->$method($endpoint);

            return true;
        } catch (Exception $exception) {
            logger($exception->getMessage());

            return false;
        }
    }

    public function updateHost(Host $host): bool
    {
        return $this->patch("host/{$host->id}", [
            'name'        => $host->name,
            'connect'     => $host->connect,
            // Notifier v2 requires description to be string.
            'description' => $host->description ?? '',
        ]);
    }

    private function patch($endpoint, $jsonPayload = [], $queryPayload = []): bool
    {
        return $this->send('patch', $endpoint, $jsonPayload, $queryPayload);
    }

    public function deleteHost(Host $host, $purgeReports = false): bool
    {
        return $this->delete("host/{$host->id}", ['status' => 'deleted'], ['complete' => $purgeReports]);
    }

    private function delete($endpoint, $jsonPayload = [], $queryPayload = []): bool
    {
        return $this->send('delete', $endpoint, $jsonPayload, $queryPayload);
    }

    public function deleteRecipient(Recipient $recipient): bool
    {
        return $this->delete("recipients/{$recipient->id}");
    }

    public function deleteTeam(Team $team): bool
    {
        return $this->delete("teams/{$team->id}");
    }

    public function sendTestMessage($recipientData): bool
    {
        return $this->post('message', $recipientData);
    }

    public function recoverEvent(Event $event): bool
    {
        return $this->patch("events/{$event->id}", [
            'timestamp' => now()->unix(),
            'type'      => 'recovery',
        ]);
    }

    public function deleteRemindersForEvent(Event $event): bool
    {
        return $this->delete("events/{$event->id}/reminders");
    }

    public function deleteRemindersForCustomCheck(CustomCheck $customCheck): bool
    {
        return $this->delete("checks/{$customCheck->id}");
    }

    public function deleteRemindersForServiceCheck(ServiceCheck $serviceCheck): bool
    {
        return $this->delete("checks/{$serviceCheck->id}");
    }

    public function deleteRemindersForSnmpCheck(SnmpCheck $snmpCheck): bool
    {
        return $this->delete("checks/{$snmpCheck->id}");
    }

    public function deleteRemindersForWebCheck(WebCheck $webCheck): bool
    {
        return $this->delete("checks/{$webCheck->id}");
    }

    public function sendEventComment(NotifyOnEventCommentData $notifyOnEventCommentData): bool
    {
        return $this->post('comments', $notifyOnEventCommentData->toArray());
    }

    public function getRecipientLogs(Recipient $recipient, $parameters)
    {
        $this->get("recipients/{$recipient->id}/logs", [
            'from'  => data_get($parameters, 'filter.from'),
            'to'    => data_get($parameters, 'filter.to'),
            'limit' => data_get($parameters, 'page.limit'),
        ]);

        return optional($this->response)->json() ?? [];
    }

    private function get($endpoint, $jsonPayload = [], $queryPayload = []): bool
    {
        return $this->send('get', $endpoint, $jsonPayload, $queryPayload);
    }

    public function getHostAlertLogs(Host $host, array $parameters)
    {
        $key = "getHostAlertLogs:{$host->id}";
        $ttl = now()->addMinute();

        return Cache::remember($key, $ttl, function () use ($host, $parameters) {
            $this->get("host/{$host->id}/events", [], [
                'from'          => data_get($parameters, 'filter.from'),
                'to'            => data_get($parameters, 'filter.to'),
                'limit'         => data_get($parameters, 'page.limit'),
                'resolved-only' => true,
            ]);

            return optional($this->response)->json() ?? [];
        });
    }
}
