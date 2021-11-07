<?php


namespace App\Support;


use BenSampo\Enum\Enum;
use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EsendexService
{

    private $config;
    /**
     * @var Response|null
     */
    private $response;

    public function __construct($config)
    {
        $this->config = $config;
    }

    private function get($endpoint, $queryPayload = [], $auth = [])
    {
        return $this->send('get', $endpoint, $queryPayload, [], $auth);
    }

    private function send($method, $endpoint, $jsonPayload = [], $queryPayload = [], $auth = []): bool
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
                ->withBasicAuth($auth['username'], $auth['password'])
                ->withBody($payload->toJson(JSON_FORCE_OBJECT), 'application/json')
                ->$method($endpoint);

            return true;
        } catch (Exception $exception) {
            logger($exception->getMessage());

            throw $exception;
        }
    }

    public function credits(string $username, string $password, string $account)
    {
        $this->get('credits', ['allocatedto' => $account], ['username' => $username, 'password' => $password]);

        $json = null;
        if (! is_null($this->response)) {
            $json = $this->response->json();

            Log::info('Response from esendex service when checking credits:', ['json' => $json]);

            if (isset($json['Total'])) {
                return $json['Total'];
            }
        }

        return 0;
    }

}
