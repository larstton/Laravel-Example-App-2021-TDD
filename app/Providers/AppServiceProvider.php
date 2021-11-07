<?php

namespace App\Providers;

use App\Enums\CheckType;
use App\Models\CustomCheck;
use App\Models\ServiceCheck;
use App\Models\SnmpCheck;
use App\Models\WebCheck;
use App\Support\CheckoutService;
use App\Support\EsendexService;
use App\Support\GitBook\GitBookPageTransformer;
use App\Support\GitBook\GitBookService;
use App\Support\MSTeamsService;
use App\Support\NotifierService;
use App\Support\Preflight\CheckPreflight;
use App\Support\Preflight\Contract\CheckPreflight as CheckPreflightContract;
use App\Support\Preflight\MockCheckPreflight;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(NotifierService::class, function () {
            $config = config('cloudradar.notifier');

            return new NotifierService($config);
        });

        $this->app->singleton(EsendexService::class, function () {
            $config = config('cloudradar.esendex');

            return new EsendexService($config);
        });

        $this->app->singleton(MSTeamsService::class, function () {
            $config = config('cloudradar.msteams');

            return new MSTeamsService($config);
        });

        $this->app->singleton(CheckoutService::class, function () {
            $config = config('cloudradar.checkout');

            return new CheckoutService($config);
        });

        $this->app->bind(CheckPreflightContract::class, function ($app) {
            if ($app->environment('local')) {
                return new MockCheckPreflight;
            }

            return new CheckPreflight;
        });

        $this->app->singleton(GitBookService::class, function () {
            $config = config('cloudradar.gitbook');

            return new GitBookService(
                $this->app->make(GitBookPageTransformer::class),
                $config['base_url'],
                $config['token'],
                $config['space_id']
            );
        });

        Builder::macro('whereLike', function ($attributes, ?string $searchTerm) {
            $this->where(function (Builder $query) use ($attributes, $searchTerm) {
                $hasRelation = function ($attribute) {
                    if (Str::contains($attribute, '.')) {
                        [$relationName, $relationAttribute] = explode('.', $attribute);

                        return $relationName !== $this->model->getTable();
                    }

                    return false;
                };
                foreach (Arr::wrap($attributes) as $attribute) {
                    $query->when(
                        $hasRelation($attribute),
                        function (Builder $query) use ($attribute, $searchTerm) {
                            [$relationName, $relationAttribute] = explode('.', $attribute);

                            $query->orWhereHas(
                                $relationName,
                                function (Builder $query) use ($relationAttribute, $searchTerm) {
                                    $query->where($relationAttribute, 'LIKE', "%{$searchTerm}%");
                                }
                            );
                        },
                        function (Builder $query) use ($attribute, $searchTerm) {
                            $query->orWhere($attribute, 'LIKE', "%{$searchTerm}%");
                        }
                    );
                }
            });

            return $this;
        });

        Stringable::macro('toMd5', function () {
            return new static(md5($this->value));
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap([
            CheckType::WebCheck     => WebCheck::class,
            CheckType::ServiceCheck => ServiceCheck::class,
            CheckType::SnmpCheck    => SnmpCheck::class,
            CheckType::CustomCheck  => CustomCheck::class,
        ]);

        Builder::macro('toFullSql', function () {
            // Replace all % with !@£ before vsprintf runs, then change back
            $sql = str_replace(['%'], ['!@£'], $this->toSql());
            $sql = vsprintf(str_replace(['?'], ['\'%s\''], $sql), $this->getBindings());

            return str_replace(['!@£'], ['%'], $sql);
        });
        \Illuminate\Database\Query\Builder::macro('toFullSql', function () {
            // Replace all % with !@£ before vsprintf runs, then change back
            $sql = str_replace(['%'], ['!@£'], $this->toSql());
            $sql = vsprintf(str_replace(['?'], ['\'%s\''], $sql), $this->getBindings());

            return str_replace(['!@£'], ['%'], $sql);
        });

        Builder::macro('ddFullSql', function () {
            dd($this->toFullSql());
        });
        \Illuminate\Database\Query\Builder::macro('ddFullSql', function () {
            dd($this->toFullSql());
        });

        // Queue::failing(function (JobFailed $event) {
        //     // (new AdminRecipient)
        //     //     ->notify(new JobFailedAdminNotification($event->job->payload(), $event->exception));
        // });

        Queue::looping(function () {
            while (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
        });
    }
}
