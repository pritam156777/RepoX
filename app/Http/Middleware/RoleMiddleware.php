<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{

    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!auth()->check()) {
            abort(403);
        }

        // FIX: split roles if passed as "super_admin|admin"
        $roles = collect($roles)
            ->flatMap(fn ($role) => explode('|', $role))
            ->toArray();

        if (!in_array(auth()->user()->role, $roles)) {
            abort(403, 'Forbidden - role mismatch issue');
        }

        return $next($request);
    }
}
