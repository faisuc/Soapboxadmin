<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Sentinel;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;

class DashboardController extends Controller
{

    public function index()
    {

        $this->_loadSharedViews();

        $data = [];

        /* Facebook Page */
        $facebook_account = $this->socialAccount->where('user_id', Sentinel::getUser()->id)->where('type_id', 1)->where('deleted_at', NULL)->orderBy('created_at', 'DESC')->get()->first();
        if(!empty($facebook_account)) {
        	
        	$this->setFacebookObject();

        	if($facebook_account->facebook_token) {
                $token = $facebook_account->facebook_token;
                $userdata = $this->api->get('/me/subscribers', $token);
                if($_SERVER['REMOTE_ADDR'] == '103.90.44.199') {
	                echo "<pre>";
	                print_r($userdata);
	                die();
                }
            }

        }
        /* Facebook Page */


        return view('pages.dashboard', $data);

    }

    public function setFacebookObject()
    {
        $app_id = getenv('FACEBOOK_CLIENT_ID');
        $app_secret = getenv('FACEBOOK_CLIENT_SECRET');
        $fb = new Facebook([
            'app_id' => $app_id,
            'app_secret' => $app_secret,
            'default_graph_version' => 'v2.5',
        ]);
        $this->api = $fb;
    }

}
