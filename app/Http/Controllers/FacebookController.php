<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Sentinel;
use Session;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;

class FacebookController extends Controller
{
	private $api;
    public function __construct()
    {
    	// Session::flush();
    	$app_id = getenv('FACEBOOK_CLIENT_ID');
    	$app_secret = getenv('FACEBOOK_CLIENT_SECRET');
		$fb = new Facebook([
			'app_id' => $app_id,
			'app_secret' => $app_secret,
			'default_graph_version' => 'v2.5',
		]);
		$this->api = $fb;
    }

    public function fb_connect_app()
    {
    	// $this->_loadSharedViews();

        $data = [];
    	if(Session::get('fb_access_token') == '')
    	{
    		$helper = $this->api->getRedirectLoginHelper();
			$permissions = ['email','user_posts','manage_pages','publish_pages'];
			$loginUrl = $helper->getLoginUrl('https://127.0.0.1:3000/fb_callback', $permissions);
			$data['loginUrl'] = $loginUrl;
    	}
        return view('pages.fb_connect_app', $data);


    }

    public function fb_callback()
    {
    	$helper = $this->api->getRedirectLoginHelper();

		try {
		  	$accessToken = $helper->getAccessToken();
		}
		catch(Facebook\Exceptions\FacebookResponseException $e) {
		  	// When Graph returns an error
		  	echo 'Graph returned an error: ' . $e->getMessage();
		  	exit;
		}
		catch(Facebook\Exceptions\FacebookSDKException $e) {
		  	// When validation fails or other local issues
		  	echo 'Facebook SDK returned an error: ' . $e->getMessage();
		  	exit;
		}


		if (! isset($accessToken)) {
		  	if ($helper->getError()) {
		    	header('HTTP/1.0 401 Unauthorized');
		    	echo "Error: " . $helper->getError() . "\n";
		    	echo "Error Code: " . $helper->getErrorCode() . "\n";
		    	echo "Error Reason: " . $helper->getErrorReason() . "\n";
		    	echo "Error Description: " . $helper->getErrorDescription() . "\n";
		  	}
		  	else {
		    	header('HTTP/1.0 400 Bad Request');
		    	echo 'Bad request';
		  	}
		  	exit;
		}

		// Logged in
		echo '<h3>Access Token</h3>';
		var_dump($accessToken->getValue());

		// The OAuth 2.0 client handler helps us manage access tokens
		$oAuth2Client = $this->api->getOAuth2Client();

		// Get the access token metadata from /debug_token
		$tokenMetadata = $oAuth2Client->debugToken($accessToken);
		echo '<h3>Metadata</h3>';
		var_dump($tokenMetadata);

		// Validation (these will throw FacebookSDKException's when they fail)
		$tokenMetadata->validateAppId($app_id); // Replace {app-id} with your app id
		// If you know the user ID this access token belongs to, you can validate it here
		//$tokenMetadata->validateUserId('123');
		$tokenMetadata->validateExpiration();

		if (! $accessToken->isLongLived()) {
		  // Exchanges a short-lived access token for a long-lived one
		  try {
		    $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
		  } catch (Facebook\Exceptions\FacebookSDKException $e) {
		    echo "<p>Error getting long-lived access token: " . $e->getMessage() . "</p>\n\n";
		    exit;
		  }

		  echo '<h3>Long-lived</h3>';
		  var_dump($accessToken->getValue());
		}

		$_SESSION['fb_access_token'] = (string) $accessToken;
		header('Location: http://127.0.0.1:3000/fb_connect_app');
    }

    public function fb_publish_post()
    {
    	$token = 'EAAkcVsx4IuIBAJ6nAs1i1pJknIUEP2xsvVdPdEkx8RY554qeXAyIFSGl1V1P1qUXUyN290jo8UA2dz8RENaZAcClZCmmdwSCZAPyNDR2RueEPLjGMZBnowiXXPG6cxCkIvPcMvgxpHI9JAE6lkYJ7V5nV5qBXf9taFvKyvgaXAZDZD';//Session::get('fb_access_token');

		$userdata = $this->api->get('/me/accounts', $token);
		$userdata = $userdata->getDecodedBody();

		foreach ($userdata['data'] as $page_key => $page) {
			$pageAccessToken = $page['access_token'];
			$facebook_page_id = $page['id'];
		}

		$message = 'scheduled post my script new script';
		// date_default_timezone_set('Asia/Kolkata');
		$current_time = date('Y-m-d h:i:s');
		$timestamp = date('Y-m-d H:i:s',strtotime("+12 minutes"));
		$timestamp = strtotime($timestamp);
		// echo $current_time.'--'.$timestamp; die();
		// echo $token; die();

		$data = array(
			'message' => $message,
			'scheduled_publish_time' => $timestamp,
			'published' => 'false'
		);

		$res = $this->api->post($facebook_page_id . '/feed/' ,$data, $pageAccessToken);
		echo "<pre>";
		print_r($res);
		die();
    }

}
