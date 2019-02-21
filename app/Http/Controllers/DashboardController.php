<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{

    public function index()
    {

        $this->_loadSharedViews();

        $data = [];

        return view('pages.dashboard', $data);

    }

}
