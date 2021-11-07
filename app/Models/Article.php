<?php

namespace App\Models;

use App\Actions\Articles\FetchArticlesFromDocs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Sushi\Sushi;

class Article extends Model
{
    use Sushi;

    public const ARTICLES_CACHE_KEY = 'cr-articles';
    public const ARTICLES_TTL_SECONDS = 60 * 60 * 24 * 30; // 30 days

    public function getRows()
    {
        return Cache::remember(
            self::ARTICLES_CACHE_KEY,
            self::ARTICLES_TTL_SECONDS,
            fn () => resolve(FetchArticlesFromDocs::class)->execute()
        );
    }
}
