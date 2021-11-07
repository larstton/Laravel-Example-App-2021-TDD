<?php

namespace App\Http\Controllers\Articles;

use App\Http\Controllers\Controller;
use App\Http\Queries\ArticleQuery;

class ArticleController extends Controller
{
    public function __invoke(ArticleQuery $query)
    {
        return $query->get();
    }
}
