<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

class ResponseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Response::macro('success', function ($message = 'Success', $statusCode = 200) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ], $statusCode);
        });

        Response::macro('error', function ($message, $error,$statusCode = 500) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'error'=>$error,
            ], $statusCode);
        });


    }
}
