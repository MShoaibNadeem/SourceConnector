<?php

use Illuminate\Http\Request;
use Illuminate\Support\Lottery;
use Illuminate\Foundation\Application;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Response;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias(
            [
                'Validation' => App\Http\Middleware\ValidationMiddleware::class,
            ]
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return Response::error("404 Not Found", "Route does not Exist", 404);
            }
        });
        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            if ($request->is('api/*')) {
                return Response::error("404 Not Found", "Resource not found", 404);
            }
        });
        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return Response::error("405 Method Not Allowed", "HTTP method is not allowed for this route", 405);
            }
        });
        $exceptions->render(function (HttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return Response::error($e->getStatusCode() . " Error", $e->getMessage(), $e->getStatusCode());
            }
        });
        $exceptions->render(function (QueryException $e, Request $request) {
            if ($request->is('api/*')) {
                return Response::error("500 Internal Server Error", "Database query error", 500);
            }
        });
        $exceptions->render(function (Exception $e, Request $request) {
            if ($request->is('api/*')) {
                return Response::error("500 Internal Server Error", "An unexpected error occurred", 500);
            }
        });

    })->create();
