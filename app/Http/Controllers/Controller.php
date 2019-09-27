<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\User;
use App\Client;
use App\Media;
use App\Post;
use App\PostNotes;
use App\ImageRepository;
use App\TextRepository;
use View;
use Sentinel;
use DB;
use Request;
use App\SocialAccount;
use App\SocialAccountInfo;
use App\SocialCell;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $user;
    protected $client;
    protected $media;
    protected $post;
    protected $postNotes;
    protected $imageRepo;
    protected $textRepo;
    protected $socialAccount;
    protected $socialAccountInfo;

    public function __construct(User $user, Client $client, Media $media, Post $post, PostNotes $postNotes, ImageRepository $imageRepo, TextRepository $textRepo, SocialAccount $socialAccount, SocialAccountInfo $socialAccountInfo, SocialCell $socialCell)
    {

        $this->user = $user;
        $this->client = $client;
        $this->media = $media;
        $this->post = $post;
        $this->postNotes = $postNotes;
        $this->imageRepo = $imageRepo;
        $this->textRepo = $textRepo;
        $this->socialAccount = $socialAccount;
        $this->socialAccountInfo = $socialAccountInfo;
        $this->socialCell = $socialCell;

    }

    public function _loadSharedViews()
    {

        $activeUser = $this->user->find(Sentinel::getUser()->id);

        View::share('timezones', $this->getTimezones());
        View::share('activeUser', $activeUser);
        View::share('userRoles', DB::table('roles')->get());
        View::share('managerLists', $this->getManagerLists());
        View::share('post_statuses', $this->post_statuses());

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

    public function clientManagers($user_id)
    {

        $clients = $this->user->select(
            DB::raw('users.*')
        )->join('clients', function($join) {
            $join->on('users.id', '=', 'clients.user_id');
        })->where('clients.client_id', '=', $user_id)
        ->get();

        return $clients;

    }

    public function post_statuses()
    {

        /*$statuses = [
            0 => 'Rejected',
            //1 => 'For Review',
            2 => 'Ready for Post',
            3 => 'Waiting Client Review',
            4 => 'Waiting Staff Review'
        ];*/
        $statuses = [
            1 => 'Approved',
            2 => 'Declined',
            3 => 'Waiting changes',
            4 => 'Waiting Client Approval',
        ];

        return $statuses;

    }

}
