<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        if (!auth()->check()) {
            return redirect(route('login'));
        }

        // Flatten all passed role arguments, supporting both comma and pipe separation
        $allowedRoles = [];
        foreach ($roles as $roleArg) {
            foreach (array_filter(preg_split('/[,|]/', $roleArg)) as $r) {
                $allowedRoles[] = trim($r);
            }
        }

        if (in_array(auth()->user()->role, $allowedRoles)) {
            return $next($request);
        }

        return redirect(route('inventory.dashboard'))
            ->with('error', 'Anda tidak memiliki akses ke halaman ini');
    }
}
