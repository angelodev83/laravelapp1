<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeoutMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && session('lastActivityTime')) {
            $inactive = time() - session('lastActivityTime');
            $sessionTimeout = config('session.lifetime') * 60; // Convert minutes to seconds

            if ($inactive >= $sessionTimeout) {
                Auth::logout();
                return redirect('/login')->with('message', 'You have been logged out due to inactivity.');
            }
        }

        session(['lastActivityTime' => time()]);

        return $next($request);
    }
}
