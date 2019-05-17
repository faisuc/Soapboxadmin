<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Sentinel;

class ContentBankController extends Controller
{

    public function index($user_id = null) {

        $this->_loadSharedViews();

        $data = [];
        $data['posts'] = [];

        if ($user_id == null)
        {
            $data['posts'] = $this->post->where('user_id', Sentinel::getUser()->id)->orderBy('created_at', 'DESC')->get();
        }
        else
        {
            $data['posts'] = $this->post->where('user_id', $user_id)->get();
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

        $data['images'] = $this->imageRepo->where('user_id', '=', $user_id)->orderBy('created_at', 'desc')->get();
        $data['texts'] = $this->textRepo->where('user_id', '=', $user_id)->orderBy('created_at', 'desc')->get();

        return view('pages.content-bank', $data);

    }

}
