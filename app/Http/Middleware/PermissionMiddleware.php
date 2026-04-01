<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, $permission = null)
    {
        $user = $request->user();

        if (!$user || !$user->is_active) {
            abort(403, 'Acceso no autorizado.');
        }

        if (!$permission || $user->isSuperAdmin()) {
            return $next($request);
        }

        $permissions = explode('|', $permission);

        foreach ($permissions as $item) {
            $item = trim($item);

            if ($item !== '' && $user->hasPermission($item)) {
                return $next($request);
            }
        }

        abort(403, 'No tienes permisos para esta acción.');
    }
}
