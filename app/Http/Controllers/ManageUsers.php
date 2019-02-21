<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Sentinel;

class ManageUsers extends Controller
{

    public function index()
    {

        $this->_loadSharedViews();

        $data = [];

        $data['users'] = Sentinel::getUserRepository()->with('roles')->where('id', '<>', Sentinel::getUser()->id)->get();

        return view('pages.manage-users', $data);

    }

}
