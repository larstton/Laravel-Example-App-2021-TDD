<?php /** @noinspection ALL */

namespace App\Http\Queries;

use App\Models\TeamMember;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class TeamMemberQuery extends QueryBuilder
{
    public function __construct()
    {
        $query = TeamMember::query()
            ->when(request()->has('search'), function ($query) {
                return $query->whereLike([
                    'users.email',
                ], request('search'));
            })->notDeleted();

        parent::__construct($query);

        $this->defaultSort('-created_at')
            ->allowedSorts([
                AllowedSort::field('email', 'users.email'),
                AllowedSort::field('date-created', 'created_at'),
                AllowedSort::field('date-updated', 'updated_at'),
            ]);
    }
}
