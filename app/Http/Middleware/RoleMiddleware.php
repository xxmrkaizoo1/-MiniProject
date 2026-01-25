<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        if (! auth()->check() || ! auth()->user()->hasRole($roles)) {
            abort(403, 'Unauthorized role');
        }

        return $next($request);
    }
}
