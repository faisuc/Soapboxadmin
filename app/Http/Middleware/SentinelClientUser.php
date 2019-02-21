<?php

namespace App\Http\Middleware;

use Closure;
use Sentinel;

class SentinelClientUser
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
        $client = Sentinel::findRoleBySlug('client');

        if ( ! $user->inRole($client))
        {
            return redirect('login');
        }

        return $next($request);

    }
}
