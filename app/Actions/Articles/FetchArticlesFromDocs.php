<?php

namespace App\Actions\Articles;

use App\Support\GitBook\GitBookService;

class FetchArticlesFromDocs
{
    private GitBookService $gitBookService;

    public function __construct(GitBookService $gitBookService)
    {
        $this->gitBookService = $gitBookService;
    }

    public function execute(): array
    {
        return $this->gitBookService->fetchMasterArticles()->all();
    }
}
