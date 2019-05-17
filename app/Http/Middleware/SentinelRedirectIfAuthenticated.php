<?php

namespace App\Http\Middleware;

use Closure;
use Sentinel;

class SentinelRedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if (Sentinel::check()) {

            $user = Sentinel::getUser();
            $admin = Sentinel::findRoleBySlug('administrator');
            $account_manager = Sentinel::findRoleBySlug('account_manager');
            $client = Sentinel::findRoleBySlug('client');

            if ($user->inRole($admin)) {
                return redirect()->intended('dashboard');
            } elseif ($user->inRole($account_manager)) {
                return redirect()->intended('dashboard');
            } elseif ($user->inRole($client)) {
                return redirect()->intended('dashboard');
            }

        }

        return $next($request);

    }
}
