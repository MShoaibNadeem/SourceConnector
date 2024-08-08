<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\AvailableSource;
use App\Http\Requests\TestConnectionRequest;
use App\Http\Requests\AvailableSourceRequest;
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
        if ($key === 'ReqValidate') {
            $id = $request->route('id');
            $source = AvailableSource::getSourceById($id);
            $type = $source->type;
            $name = $source->name;
            // Merge the fetched data into the request
            $request->merge([
                // 'type'=>'API',
                // 'name'=>'connector'
                'type' => $type,
                'name' => $name,
            ]);
            app(TestConnectionRequest::class);
        }
        return $next($request);
    }
}
