<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'Sesión expirada.'], 401)
                : redirect()->route('admin.login');
        }

        $user = Auth::user();
        if (!$user->is_active || !in_array($user->role, ['superadmin', 'admin', 'editor', 'periodista'], true)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('admin.login')->with('error', 'Tu cuenta está desactivada o no tiene acceso al panel.');
        }

        return $next($request);
    }
}