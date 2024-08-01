<?php

namespace App\Http\Middleware;

use App\Http\Requests\AvailableSourceRequest;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next , $key): Response
    {

        if ($key === 'Source') {
            app(AvailableSourceRequest::class);
        }
        return $next($request);
    }
}
