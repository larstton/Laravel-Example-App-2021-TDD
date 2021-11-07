<?php

namespace App\Support\GitBook;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class GitBookPageTransformer
{
    public function transform(Collection $data): Collection
    {
        return $this->transformPages($data)
            ->map(fn ($page) => tap($page, function (&$page) {
                data_set($page, 'recommended', Str::contains(data_get($page, 'title', ''), '⭐'));
            }));
    }

    private function transformPages(Collection $pages, string $parentPath = '/'): Collection
    {
        $finishCallback = function (&$page, $path, $parentPath) {
            data_set($page, 'path', $path);
            data_set($page, 'category', (string) str($parentPath)->trim('/')->replace('-', ' ')->title());
            unset($page['pages']);
        };

        return $pages->flatMap(function ($page) use ($finishCallback, $parentPath) {
            $path = str($parentPath)
                ->append(str($page['path'])->start('/'))
                ->start('/')
                ->rtrim('/');

            if ($subPages = data_get($page, 'pages', false)) {
                $subPages = $this->transformPages(collect($subPages), $path);
                $finishCallback($page, $path, $parentPath);

                return $subPages->prepend($page);
            }

            $finishCallback($page, $path, $parentPath);

            return [$page];
        });
    }
}
