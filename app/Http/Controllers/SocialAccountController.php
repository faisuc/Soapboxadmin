<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Sentinel;

class SocialAccountController extends Controller
{

    public function index($user_id = null)
    {

        $this->_loadSharedViews();

        $data = [];
        $data['socials'] = [];

        if ($user_id == null)
        {
            $data['socials'] = $this->socialAccount->where('user_id', Sentinel::getUser()->id)->orderBy('created_at', 'DESC')->get();
        }
        else
        {
            $data['socials'] = $this->socialAccount->where('user_id', $user_id)->get();
        }

        if (is_admin())
        {
            $data['managedClients'] = Sentinel::getUserRepository()->with('roles')->where('id', '<>', Sentinel::getUser()->id)->get();
        }
        else
        {
            $data['managedClients'] = $this->user->find(Sentinel::getUser()->id)->clients();
        }

        $user_id = $user_id ? $user_id : Sentinel::getUser()->id;

        return view('pages.social-accounts', $data);

    }

    public function store(Request $request)
    {

        $name = $request->input('name');
        $url = $request->input('url');
        $type_id = $request->input('social_account');

        $social = new $this->socialAccount;
        $social->type_id = $type_id;
        $social->user_id = Sentinel::getUser()->id;
        $social->name = $name;
        $social->url = $url;
        $social->save();

        return redirect()->back()->with('flash_message', 'Social account has been added.');

    }

    public function delete($social_id = null)
    {

        if ($social_id)
        {

            $this->socialAccount->destroy($social_id);

            return redirect()->back()->with('flash_message', 'Social account has been unlinked.');

        }

    }

}
