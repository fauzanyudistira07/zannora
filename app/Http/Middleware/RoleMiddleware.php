<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            return redirect()->route('login');
        }

        if ($roles !== [] && ! in_array($request->user()->role, $roles, true)) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            if ($request->is('admin/*')) {
                return redirect()
                    ->route('admin.dashboard')
                    ->with('status', 'Akses halaman tersebut tidak tersedia untuk role Anda.');
            }

            return redirect()
                ->route('dashboard')
                ->with('status', 'Akses halaman tersebut tidak tersedia untuk role Anda.');
        }

        return $next($request);
    }
}
