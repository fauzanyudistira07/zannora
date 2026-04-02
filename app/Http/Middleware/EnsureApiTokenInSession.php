<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiTokenInSession
{
    /**
     * Ensure authenticated web users always have a valid Sanctum token
     * available in session for internal API calls from Blade pages.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        $currentToken = (string) $request->session()->get('api_token', '');
        $tokenId = $currentToken !== '' ? explode('|', $currentToken)[0] ?? null : null;
        $tokenExists = $tokenId ? $user->tokens()->whereKey($tokenId)->exists() : false;

        if (! $tokenExists) {
            $plainToken = $user->createToken('web-portal')->plainTextToken;
            $request->session()->put('api_token', $plainToken);
        }

        return $next($request);
    }
}

