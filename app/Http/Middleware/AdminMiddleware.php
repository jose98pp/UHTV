<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            if ($request->expectsJson() || $request->ajax() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'La sesión del administrador expiró. Vuelve a iniciar sesión.',
                ], 401);
            }

            return redirect()->route('admin.login')->with('error', 'Debes iniciar sesión para acceder al panel de administración.');
        }

        if (Auth::user()->role !== 'admin') {
            if ($request->expectsJson() || $request->ajax() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para realizar esta acción.',
                ], 403);
            }

            return redirect()->route('portada')->with('error', 'No tienes permisos para acceder al panel de administración.');
        }

        return $next($request);
    }

}
