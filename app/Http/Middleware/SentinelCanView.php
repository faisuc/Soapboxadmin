<?php

namespace App\Http\Middleware;

use Closure;
use Sentinel;

class SentinelCanView
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
        $user = Sentinel::getUser();
        $admin = Sentinel::findRoleBySlug('administrator');
        $account_manager = Sentinel::findRoleBySlug('account_manager');

        if ( ! $user->inRole($admin) && ! $user->inRole($account_manager))
        {
            return redirect('login');
        }

        return $next($request);
    }
}
