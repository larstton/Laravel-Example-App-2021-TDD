<?php

namespace App\Http\Queries;

use App\Models\Article;
use Spatie\QueryBuilder\QueryBuilder;

class ArticleQuery extends QueryBuilder
{
    public function __construct()
    {
        $query = Article::query()
            ->when(filled(request('search')), fn ($query) => $query->whereLike([
                'title', 'description',
            ], request('search')));

        parent::__construct($query);
    }
}
