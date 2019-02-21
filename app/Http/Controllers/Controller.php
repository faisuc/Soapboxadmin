<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\User;
use App\Client;
use View;
use Sentinel;
use DB;
use Request;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $user;
    protected $client;

    public function __construct(User $user, Client $client)
    {

        $this->user = $user;
        $this->client = $client;

    }

    public function _loadSharedViews()
    {

        $activeUser = $this->user->find(Sentinel::getUser()->id);

        View::share('timezones', $this->getTimezones());
        View::share('activeUser', $activeUser);
        View::share('userRoles', DB::table('roles')->get());
        View::share('managerLists', $this->getManagerLists());

        if (Request::route('user_id'))
        {
            View::share('routeUser', $this->user->find(Request::route('user_id')));
        }

    }

    public function getManagerLists()
    {

        $users = $this->user->select(
            DB::raw('users.*')
        )->join('role_users', function($join) {
            $join->on('users.id', '=', 'role_users.user_id');
        })->join('roles', function($join) {
            $join->on('role_users.role_id', '=', 'roles.id');
        })->where('roles.slug', '<>', 'client')
        ->get();

        return $users;

    }

    public function getTimezones()
    {

        $timezoneList = \DateTimeZone::listIdentifiers(\DateTimezone::ALL);

        return $timezoneList;

    }

    public function getManagedClients($user_id)
    {

        $clients = $this->user->select(
            DB::raw('users.*')
        )->join('clients', function($join) {
            $join->on('users.id', '=', 'clients.client_id');
        })->where('clients.user_id', '=', $user_id)
        ->get();

        return $clients;

    }

}
