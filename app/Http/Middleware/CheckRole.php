<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')->with('error', 'Please sign in to access this page.');
        }

        $user = Auth::user();

        if (!$user->status === 'active') {
            Auth::logout();
            return redirect()->route('auth.login')->with('error', 'Your account is suspended or inactive.');
        }

        if (!$user->hasRole($roles)) {
            abort(403, 'Unauthorized. Your role (' . ($user->role ? $user->role->display_name : 'None') . ') is not authorized to access this resource.');
        }

        return $next($request);
    }
}
