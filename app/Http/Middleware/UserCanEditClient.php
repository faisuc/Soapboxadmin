<?php

namespace App\Http\Middleware;

use Closure;
use Sentinel;
use Request;

class UserCanEditClient
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

        if ( ! $user->inRole($admin) && ! canManageClient(Sentinel::getUser()->id, Request::route('user_id')))
        {
            return redirect('login');
        }

        return $next($request);

    }
}
