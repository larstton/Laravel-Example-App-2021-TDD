<?php

namespace App\Support\GitBook;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class GitBookService
{
    private $response;
    private GitBookPageTransformer $pageTransformer;
    private $baseUrl;
    private $token;
    private $spaceId;

    public function __construct(GitBookPageTransformer $pageTransformer, $baseUrl, $token, $spaceId)
    {
        $this->pageTransformer = $pageTransformer;
        $this->baseUrl = $baseUrl;
        $this->token = $token;
        $this->spaceId = $spaceId;
    }

    public function fetchMasterArticles(): Collection
    {
        if ($this->get("spaces/{$this->spaceId}/content/v/master")) {
            return $this->transform($this->getJsonResponse());
        }

        return collect();
    }

    private function get($endpoint)
    {
        return $this->send('get', $endpoint);
    }

    private function send($method, $endpoint)
    {
        try {
            $this->response = Http::timeout(5)
                ->retry(3, 100)
                ->baseUrl($this->baseUrl)
                ->withToken($this->token)
                ->$method($endpoint);

            return true;
        } catch (Exception $exception) {
            logger($exception->getMessage());

            return false;
        }
    }

    private function transform(array $data): Collection
    {
        return $this->pageTransformer->transform(collect(data_get($data, 'page.pages')));
    }

    public function getJsonResponse()
    {
        return $this->getResponse()->json();
    }

    public function getResponse()
    {
        return $this->response;
    }
}
