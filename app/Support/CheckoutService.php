<?php

namespace App\Support;

use App\Models\Team;
use App\Models\User;
use Exception;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckoutService
{
    private $config;
    private $response;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function makeCardUpdateUrl(User $user)
    {
        $encryptedData = $this->encryptDataPayload([
            'teamUuid' => $user->team_id,
            'userUuid' => $user->id,
        ]);

        return "{$this->getCheckoutBaseUrl()}/card-update/{$encryptedData}";
    }

    private function encryptDataPayload($payload): string
    {
        return JWT::encode($payload, $this->config['jwt_token']);
    }

    public function getCheckoutBaseUrl()
    {
        return $this->config['base_url'];
    }

    public function makeLoginUrl(User $user)
    {
        $encryptedData = $this->encryptDataPayload([
            'user' => array_merge_recursive([
                'teamUuid' => $user->team_id,
                'uuid'     => $user->id,
            ], $user->toArray()),
        ]);

        return "{$this->getCheckoutBaseUrl()}/login/{$encryptedData}";
    }

    public function getJsonResponse()
    {
        return $this->getResponse()->json();
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function getTeam(Team $team)
    {
        try {
            $this->get("api/team/{$team->id}");

            $json = null;
            if (! is_null($this->response)) {
                $json = $this->response->json();

                Log::info('Response from checkout service when fetching team:', ['json' => $json]);
                if (isset($json['success']) && $json['success']) {
                    return true;
                }
            }

            Log::error('Error get user info from checkout service.', ['json' => $json]);

            return false;
        } catch (Exception $exception) {
            Log::error(
                "Exception get user info from checkout service. {$exception->getMessage()}"
            );

            return false;
        }
    }

    private function get($endpoint, $jsonPayload = [], $queryPayload = [])
    {
        return $this->send('get', $endpoint, $jsonPayload, $queryPayload);
    }

    private function send($method, $endpoint, $jsonPayload = [], $queryPayload = [])
    {
        $payload = collect([
            'query' => $queryPayload,
            'json'  => $jsonPayload,
        ])->reject(fn ($item) => empty($item));

        try {
            $this->response = Http::timeout($this->config['timeout'])
                ->retry(3, 100)
                ->baseUrl($this->getCheckoutBaseUrl())
                ->withBasicAuth($this->config['username'], $this->config['password'])
                ->withHeaders($this->config['headers'])
                ->withoutVerifying()
                ->$method($endpoint, $payload->all());

            return true;
        } catch (Exception $exception) {
            logger($exception->getMessage());

            return false;
        }
    }

    public function teamHasUnpaidInvoices(Team $team)
    {
        if ($this->teamHasUnpaidInvoicesRequest($team)) {
            $invoiceData = $this->getResponse();

            if ($invoiceData['has_unpaid_invoices']) {
                return true;
            }
        }

        return false;
    }

    private function teamHasUnpaidInvoicesRequest(Team $team)
    {
        try {
            $this->get("api/team-invoice-payment-status/{$team->id}");

            $json = null;
            if (! is_null($this->response)) {
                $json = $this->response->json();

                Log::info('Response from checkout service when fetching unpaid invoices data:', ['json' => $json]);
                if (isset($json['success']) && $json['success']) {
                    return true;
                }
            }

            Log::error('Error get unpaid invoices data from checkout service.', ['json' => $json]);

            return false;
        } catch (Exception $exception) {
            Log::error(
                "Exception get unpaid invoices data from checkout service. {$exception->getMessage()}"
            );

            return false;
        }
    }

    public function deleteTeam(Team $team)
    {
        try {
            $this->delete("api/user/{$team->id}");

            $json = null;
            if (! is_null($this->response)) {
                $json = $this->response->json();

                Log::info('Response from checkout service when removing user:', ['json' => $json]);
                if (isset($json['success']) && $json['success']) {
                    return true;
                }
            }

            Log::error('Deleting user and plan at checkout service failed.', ['json' => $json]);

            return false;
        } catch (Exception $exception) {
            Log::error(
                "Got error while trying to delete user at checkout service. {$exception->getMessage()}"
            );

            return false;
        }
    }

    private function delete($endpoint, $jsonPayload = [], $queryPayload = [])
    {
        return $this->send('delete', $endpoint, $jsonPayload, $queryPayload);
    }
}
