<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $roles)
    {
        if (!auth()->check()) {
            return redirect(route('login'));
        }

        // Parse roles - can be comma or pipe separated
        $allowedRoles = array_filter(explode('|', str_replace(',', '|', $roles)));
        
        if (in_array(auth()->user()->role, $allowedRoles)) {
            return $next($request);
        }

        return redirect(route('inventory.dashboard'))
            ->with('error', 'Anda tidak memiliki akses ke halaman ini');
    }
}
