<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Sentinel;
use Validator;
use App\Http\Requests\LoginFormRequest;

class LoginController extends Controller
{

    public function index()
    {

        return view('auth.login');

    }

    public function postLogin(LoginFormRequest $request)
    {

        $input = $request->only('login', 'password');

        try {

            if (Sentinel::authenticate($input, $request->has('remember')))
            {
                return $this->redirectWhenLoggedIn();
            }

            // return redirect()->back()->withInput($request->except('password'))->withErrors('Invalid credentials provided');
            return redirect()->back()->withErrors(['Invalid credentials provided']);

        } catch (\Cartalyst\Sentinel\Checkpoints\NotActivatedException $e) {
            // return redirect()->back()->withInput()->withErrorMessage('User not activated.');
            return redirect()->back()->withErrors(['User not activated.']);
        } catch (\Cartalyst\Sentinel\Checkpoints\ThrottlingException $e) {
            // return redirect()->back()->withInput()->withErrorMessage($e->getMessage());
            return redirect()->back()->withErrors($e->getMessage());
        }

    }

    protected function redirectWhenLoggedIn()
    {

        $user = Sentinel::getUser();
        $admin = Sentinel::findRoleBySlug('administrator');
        $account_manager = Sentinel::findRoleBySlug('account_manager');
        $client = Sentinel::findRoleBySlug('client');

        if ($user->inRole($admin)) {
            return redirect()->intended('dashboard');
        } elseif ($user->inRole($account_manager)) {
            return redirect()->intended('dashboard');
        } elseif ($user->inRole($client)) {
            return redirect()->intended('dashboard');
        }

    }

    public function logout()
    {

        Sentinel::logout();

        return redirect('/login');

    }

}
