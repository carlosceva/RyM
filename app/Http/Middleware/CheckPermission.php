<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permission): Response
    {
        if (!auth()->check() || !auth()->user()->can($permission)) {
            // Si no tiene el permiso, redirige a la página de acceso denegado
            return response()->view('acceso-no-autorizado', [], 403);
        }

        return $next($request); // Si tiene el permiso, permite el acceso
    }
}
