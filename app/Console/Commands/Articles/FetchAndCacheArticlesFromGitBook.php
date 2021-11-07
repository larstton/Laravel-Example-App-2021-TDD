<?php

namespace App\Console\Commands\Articles;

use App\Actions\Articles\FetchArticlesFromDocs;
use App\Models\Article;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class FetchAndCacheArticlesFromGitBook extends Command
{
    protected $signature = 'cloudradar:fetch-and-cache-articles';
    protected $description = 'Caches all articles from docs.cloudradar.io';

    public function handle()
    {
        if (filled($data = resolve(FetchArticlesFromDocs::class)->execute())) {
            Cache::forget(Article::ARTICLES_CACHE_KEY);
            Cache::add(Article::ARTICLES_CACHE_KEY, $data, Article::ARTICLES_TTL_SECONDS);

            $this->info('Data fetched and cached.');
            return 0;
        }

        $this->error('Something went wrong.');

        return 1;
    }
}
