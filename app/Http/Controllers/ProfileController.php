<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Mail\Mailable;
use Sentinel;
use Mail;
use URL;
use DB;
use Illuminate\Support\Facades\Input;
use Cartalyst\Sentinel\Laravel\Facades\Activation;

use Illuminate\Support\Facades\Storage;

use Carbon\Carbon;
use Session;
use Redirect;


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

    public function register()
    {
        $data = array();
        return view('pages.signup', $data);
    }

    public function create_user(Request $request)
    {   
        $validatedData = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'phone' => 'required|numeric',
            'password' => 'required|min:5|max:20',
        ]);

        $checkUser = $this->user->where('email',$request->input('email'))->get();
        if($checkUser->isEmpty()) {
            $user = $this->user;
            $user->first_name = $request->input('first_name');
            $user->last_name = $request->input('last_name');
            $user->email = $request->input('email');
            $user->phone_number = $request->input('phone');
            $user->password = $this->hash($request->input('password'));
            $user->save();

            $role = Sentinel::findRoleBySlug($request->input('roles'));
            $role->users()->attach($user);

            $activation = Activation::create($user);
            $activation_code = $activation->code;
            
            $name = $user->first_name.' '.$user->last_name;
            $verify_link = URL::to('/').'/verify_register_user?user_id='.$user->id.'&code='.$activation_code;
            $html = 'Hi '.$name.',<br>'.'You are registered successfully. <a href="'.$verify_link.'">Click here</a> to verify';
            $user->html = $html;
            Mail::send([], [], function ($message) use ($user) { 
                $name = $user->first_name.' '.$user->last_name;
                $html = $user->html;
                $message->to($user->email, $name)->subject('subject')->setBody($html, 'text/html'); 
            });
            
            return redirect('/signup')->with('flash_message', 'Registered Successfully. Please Check your email to verfiy your account and login.');
        }
        else {
            
            return redirect('/signup')->withErrors(['Email Already Registered.']);
        }

    }

    public function verify_register_user()
    {
        $code = Input::get('code');
        $id = Input::get('user_id');
        $user = Sentinel::findById($id);
        $user_id = $user->id;
        echo $user_id.'<br>';
        $activation_data = DB::select("SELECT * FROM `activations` WHERE user_id = ".$user_id);
        if(!empty($activation_data)) {
            return redirect('/login')->withErrors(['Already Activated.!!!!']);
        }
        else {

            if (!Activation::complete($user, $code)) {
                // return redirect()->back()->withErrors(['Something went wrong.. Please try again later.']);
                return redirect('/login')->withErrors(['Something went wrong.. Please try again later.']);
            }
            else {
                // return redirect()->back()->with('flash_message', 'Please Login..');
                return redirect('/login')->with('flash_message', 'User Activated. You can login now.');    
            }
        }
    }

    public function hash($string)
    {
        // Usually caused by an old PHP environment, see
        // https://github.com/cartalyst/sentry/issues/98#issuecomment-12974603
        // and https://github.com/ircmaxell/password_compat/issues/10
        if (!function_exists('password_hash')) {
            throw new \RuntimeException('The function password_hash() does not exist, your PHP environment is probably incompatible. Try running [vendor/ircmaxell/password-compat/version-test.php] to check compatibility or use an alternative hashing strategy.');
        }

        if (($hash = password_hash($string, PASSWORD_DEFAULT)) === false) {
            throw new \RuntimeException('Error generating hash from string, your PHP environment is probably incompatible. Try running [vendor/ircmaxell/password-compat/version-test.php] to check compatibility or use an alternative hashing strategy.');
        }

        return $hash;
    }

}
