<?php

namespace App\Http\Queries;

use App\Models\Recipient;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class RecipientQuery extends QueryBuilder
{
    public function __construct()
    {
        $query = Recipient::query()
            ->when(filled(request('search')), function ($query) {
                return $query->whereLike([
                    'sendto', 'description'
                ], request('search'));
            });

        parent::__construct($query);

        $this->defaultSort('-created_at')
            ->allowedSorts([
                AllowedSort::field('date-created', 'created_at'),
                AllowedSort::field('date-updated', 'updated_at'),
                AllowedSort::field('media-type', 'media_type'),
                AllowedSort::field('send-to', 'sendto'),
                AllowedSort::field('description'),
                AllowedSort::field('permanent-failures-last-24h', 'permanent_failures_last_24_h'),
            ]);
    }
}
