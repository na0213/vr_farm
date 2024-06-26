<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                if (($guard === 'admins') && $request->routeIs('admin.*')) {
                    return redirect(RouteServiceProvider::ADMIN_HOME);
                }
                if (($guard === 'owners') && $request->routeIs('owner.*')) {
                    return redirect(RouteServiceProvider::OWNER_HOME);
                }
                if (($guard === 'shops') && $request->routeIs('shop.*')) {
                    return redirect(RouteServiceProvider::SHOP_HOME);
                }

                return redirect(RouteServiceProvider::HOME);
            }
        }
        // foreach ($guards as $guard) {
        //     if (Auth::guard($guard)->check()) {
        //         return redirect(RouteServiceProvider::HOME);
        //     }
        // }

        return $next($request);
    }
}
