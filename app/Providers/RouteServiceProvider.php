<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {

            // The API used by the frontend at my.cloudradar.{tld}/engine
            $this->mapEngineRoutes();

            // Customer API which we expose to CR users.
            $this->mapApiRoutes();

            // Internal company API
            $this->mapLoopholeRoutes();

            $this->mapKlickRoutes();

            $this->mapWebRoutes();

        });
    }

    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            if (auth()->user()) {
                return [
                    Limit::perMinute(500),
                    Limit::perMinute(60)
                        ->by('rate-limit:api:'.auth()->user()->team_id),
                ];
            }

            return Limit::perMinute(60);
        });

        RateLimiter::for('engine', function (Request $request) {
            if (auth()->user()) {
                return [
                    Limit::perMinute(500),
                    Limit::perMinute(120)
                        ->by('rate-limit:engine:'.auth()->user()->team_id),
                ];
            }

            return Limit::perMinute(60)->by($request->fingerprint());
        });

        RateLimiter::for('klick', function (Request $request) {
            return Limit::perMinute(10)->by($request->fingerprint());
        });

        RateLimiter::for('loophole', function (Request $request) {
            return Limit::perMinute(100)->by($request->fingerprint());
        });

        RateLimiter::for('email-verification', function (Request $request) {
            // Max. 1 request / min by url+params (will include recipient ID if applicable)
            return Limit::perMinute(1)->by(
                $request->fingerprint().sha1(json_encode($request->route()->parameters()))
            );
        });

        RateLimiter::for('sending-test-message', function (Request $request) {
            // Max. 5 requests / min / team.
            return Limit::perMinute(5)
                ->by('rate-limit:sending-test-message-2:'.current_team()->id);
        });
    }

    protected function mapEngineRoutes()
    {
        Route::middleware('engine')
            ->domain(config('app.engine_url'))
            ->name('engine.')
            ->group(base_path('routes/engine.php'));
    }

    protected function mapApiRoutes()
    {
        Route::middleware('api')
            ->domain(config('app.api_url'))
            ->name('api.')
            ->group(base_path('routes/api.php'));
    }

    protected function mapLoopholeRoutes()
    {
        Route::middleware('loophole')
            ->domain(config('app.loophole_url'))
            ->name('loophole.')
            ->group(base_path('routes/loophole.php'));
    }

    protected function mapKlickRoutes()
    {
        Route::middleware('klick')
            ->domain(config('app.klick_url'))
            ->name('klick.')
            ->group(base_path('routes/klick.php'));
    }

    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->domain(config('app.app_url'))
            ->name('web.')
            ->group(base_path('routes/web.php'));
    }
}
