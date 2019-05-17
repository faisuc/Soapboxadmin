<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Sentinel;

class MessageController extends Controller
{

    public function index()
    {

        $this->_loadSharedViews();

        $data = [];

        if (is_admin())
        {
            $data['clients'] = Sentinel::getUserRepository()->with('roles')->where('id', '<>', Sentinel::getUser()->id)->get();
        }
        elseif (is_accountManager())
        {
            $data['clients'] = $this->getManagedClients(Sentinel::getUser()->id);
        }
        else
        {
            $data['clients'] = $this->clientManagers(Sentinel::getUser()->id);
        }

        return view('pages.messages', $data);

    }

}
