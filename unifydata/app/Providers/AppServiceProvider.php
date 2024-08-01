<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use OpenAI;

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
        //
    }


}
