<?php

namespace Tests\Unit\Actions\Articles;

use App\Support\GitBook\GitBookService;
use Mockery\MockInterface;
use Tests\TestCase;
use App\Actions\Articles\FetchArticlesFromDocs;

class GetArticlesActionTest extends TestCase
{
    /** @test */
    public function can_get_articles_from_gitbook()
    {
        $this->mock(GitBookService::class, function(MockInterface $mock) {
            $mock->shouldReceive('fetchMasterArticles->all')
                ->andReturn([]);
        });

        resolve(FetchArticlesFromDocs::class)->execute();
    }
}
