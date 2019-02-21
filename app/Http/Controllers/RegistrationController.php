<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Sentinel;

class RegistrationController extends Controller
{

    public function postRegister(Request $request)
    {

        $user = Sentinel::registerAndActivate($request->all());

        $role = Sentinel::findRoleBySlug('administrator');

        $role->users()->attach($user);

    }

}
