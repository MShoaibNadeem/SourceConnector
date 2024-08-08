<?php

namespace App\Providers;

use OpenAI;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(OpenAI\Client::class, function ($app) {
            return OpenAI::client(env('OPENAI_API_KEY'));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(2)->by($request->user()?->id ?: $request->ip());
        });
    }


}
