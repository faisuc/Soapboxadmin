<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Sentinel;

class ProfileController extends Controller
{

    public function index()
    {

        $this->_loadSharedViews();

        $data = [];

        return view('pages.user-profile', $data);

    }

    public function update(Request $request, $user_id = null)
    {

        $validatedData = $request->validate([
            'first_name' => 'required|min:3',
            'last_name'  => 'required|min:3',
            'timezone'   => 'required',
            'password'   => 'nullable|confirmed|min:6',
            'password_confirmation' => 'nullable|min:6',
            'profilephoto' => 'nullable|image|max:2048|mimes:jpg,jpeg,png,gif',
            'roles' => $request->has('roles') ? 'required' : ''
        ]);

        $credentials = [
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'company_name' => $request->input('company_name'),
            'timezone' => $request->input('timezone'),
        ];

        if ($request->has('profilephoto'))
        {

            $photo = $request->file('profilephoto');
            $fileName = uniqid() . $photo->getClientOriginalName();
            $filePath = '/public/user/profile/' . $fileName;
            $photo->storeAs('/public/user/profile/', $fileName);
            $credentials['profilephoto'] = $fileName;
        }

        if ($request->has('managers'))
        {

            $this->client->where('client_id', $user_id)->delete();

            foreach ($request->input('managers') as $manager_id)
            {
                $this->client = new \App\Client;
                $this->client->user_id = $manager_id;
                $this->client->client_id = $user_id;
                $this->client->save();
            }

        }

        if (null != $user_id)
        {

            $user = Sentinel::findByCredentials([
                'email' => $this->user->find($user_id)->email
            ]);

        }
        else
        {

            $user = Sentinel::findByCredentials([
                'email' => $request->input('email')
            ]);

        }

        if ( ! empty($request->input('password')))
        {
            $credentials['password'] = $request->input('password');
        }

        Sentinel::update($user, $credentials);

        if ($request->has('roles'))
        {

            $role = Sentinel::findRoleBySlug($request->input('roles'));

            $userRoles = $user->roles->pluck('slug')->toArray();

            if ($userRoles[0] != $request->input('roles'))
            {
                $detachRole = Sentinel::findRoleBySlug($userRoles[0]);
                $user->roles()->detach($detachRole);
                $role->users()->attach($user);

            }

        }

        return redirect()->back()->with('flash_message', 'User has been updated.');

    }

    public function create()
    {

        $this->_loadSharedViews();

        return view('pages.profile-create');

    }

    public function store(Request $request)
    {

        $validatedData = $request->validate([
            'email' => 'required|email|unique:users,email',
            'first_name' => 'required|min:3',
            'last_name'  => 'required|min:3',
            'timezone'   => 'required',
            'roles' => 'required',
            'password'   => 'required|confirmed|min:6',
            'password_confirmation' => 'required|min:6',
            'profilephoto' => 'nullable|image|max:2048|mimes:jpg,jpeg,png,gif'
        ]);

        $credentials = [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'company_name' => $request->input('company_name'),
            'timezone' => $request->input('timezone'),
        ];

        if ($request->has('profilephoto'))
        {

            $photo = $request->file('profilephoto');
            $fileName = uniqid() . $photo->getClientOriginalName();
            $filePath = '/public/user/profile/' . $fileName;
            $photo->storeAs('/public/user/profile/', $fileName);
            $credentials['profilephoto'] = $fileName;
        }

        $user = Sentinel::registerAndActivate($credentials);
        $role = Sentinel::findRoleBySlug($request->input('roles'));
        $role->users()->attach($user);

        if ($request->has('managers'))
        {

            foreach ($request->input('managers') as $manager_id)
            {
                $this->client = new \App\Client;
                $this->client->user_id = $manager_id;
                $this->client->client_id = $user->id;
                $this->client->save();
            }

        }

        return redirect()->back()->with('flash_message', 'New user has been created.');

    }

    public function delete($user_id = null)
    {

        if (null != $user_id)
        {
            $this->user->destroy($user_id);

            return redirect()->back()->with('flash_message', 'User has been deleted.');
        }

        return redirect()->back();

    }

    public function edit($user_id = null)
    {

        if (null != $user_id)
        {

            $this->_loadSharedViews();

            $currentUser = $this->user->find($user_id);

            $data = [];
            $data['currentUser'] = $currentUser;

            if ($currentUser)
            {

                return view('pages.profile-edit', $data);

            }

            return redirect()->back();

        }

        return redirect()->back();

    }

    public function myclients()
    {

        $this->_loadSharedViews();
        $data = [];
        $data['managedClients'] = $this->getManagedClients(Sentinel::getUser()->id);

        return view('pages.my-clients', $data);

    }

}
