<?php


namespace App\Support;


use BenSampo\Enum\Enum;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MSTeamsService
{
    private $config;
    private $response;

    public function __construct($config)
    {
        $this->config = $config;
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
            logger('Sending msteams data', [
                'data'     => $payload->all(),
                'asJson'   => $payload->toJson(),
                'config'   => $this->config,
                'endpoint' => $endpoint,
            ]);
            $this->response = Http::timeout($this->config['timeout'])
                ->retry(3, 100)
                //for some reason using JSON_FORCE_OBJECT as option for $payload->toJson()
                //resulted in 400 response from MSTeams webhook
                //with "Bad payload received by generic incoming webhook" message
                ->withBody($payload->toJson(),'application/json')
                ->$method($endpoint);

            return true;
        }catch (\Exception $exception) {
            logger($exception->getMessage());

            return false;
        }
    }

    public function test($url)
    {

        $message = [
            '@type'      => 'MessageCard',
            '@context'   => 'https://schema.org/extensions',
            'summary'    => 'Test Message ',
            'themeColor' => '0078D7',
            'sections'   =>
                [
                    [
                        'activityTitle'    => 'CloudRadar',
                        'activitySubtitle' => "CloudRadar Test",
                        'activityImage'    => 'https://img.icons8.com/nolan/48/2ecc71/sent.png',
                        'text'             => 'Test message to validate msteams url',
                    ],
                ],
        ];

        if ($this->post($url, $message)) {

            Log::info('Response from msteams service when checking url:', ['body' => $this->response->body()]);

            return $this->response->body() === "1";
        }

        return false;
    }
}
