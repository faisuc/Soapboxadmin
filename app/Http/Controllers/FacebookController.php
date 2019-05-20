<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Sentinel;
use Session;
use Redirect;
use URL;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;

class FacebookController extends Controller
{
	private $api;
	public function setFacebookObject()
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
		$this->_loadSharedViews();

        $data = [];

        $this->setFacebookObject();

        /*if(Session::get('fb_access_token') == '')
    	{
    		$helper = $this->api->getRedirectLoginHelper();
			$permissions = ['email','user_posts','manage_pages','publish_pages'];
			$loginUrl = $helper->getLoginUrl(URL::to('/').'/fb_callback', $permissions);
			$data['loginUrl'] = $loginUrl;
    	}*/

        return view('pages.fb_connect_app', $data);
    }

    public function fb_callback()
    {
    	$app_id = getenv('FACEBOOK_CLIENT_ID');
    	$this->setFacebookObject();

    	$helper = $this->api->getRedirectLoginHelper();
    	if (isset($_GET['state'])) {
            $helper->getPersistentDataHandler()->set('state', $_GET['state']);
        }
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

		// $_SESSION['fb_access_token'] = (string) $accessToken;
		$accessToken = (string) $accessToken;
		Session::put('fb_access_token', $accessToken);
		// echo Session::get('fb_access_token'); die();
		// header('Location: http://127.0.0.1:3000/fb_connect_app');
		$fb_connect_url = URL::to('/').'/fb_publish_post';
		return redirect()->away($fb_connect_url);
    }

    public function fb_publish_post(Request $request)
    {
		$validatedData = $request->validate([
            'message' => 'required|min:3',
            'timestamp' => 'required|after:12 minutes'
        ]);

        /*$current_time = date('Y-m-d h:i:s');
		$next_timestamp = date('Y-m-d h:i A',strtotime("+12 minutes"));*/
		
    	$this->setFacebookObject();

    	if(Session::get('fb_access_token') == '')
    	{
    		$helper = $this->api->getRedirectLoginHelper();
			$permissions = ['email','user_posts','manage_pages','publish_pages'];
			$loginUrl = $helper->getLoginUrl(URL::to('/').'/fb_callback', $permissions);
			return redirect()->away($loginUrl);
			echo "Not Redirecting. Error Occur"; die();
    	}

    	$token = Session::get('fb_access_token');

		$userdata = $this->api->get('/me/accounts', $token);
		$userdata = $userdata->getDecodedBody();

		foreach ($userdata['data'] as $page_key => $page) {
			$pageAccessToken = $page['access_token'];
			$facebook_page_id = $page['id'];
		}

		date_default_timezone_set('Asia/Kolkata');
		// $message = 'scheduled post my script new script';
		// echo $current_time.'--'.$timestamp; die();
		// echo $token; die();
		$message = $request->input('message');
        $timestamp = $request->input('timestamp');
		$timestamp = strtotime($timestamp);

		$data = array(
			'message' => $message,
			'scheduled_publish_time' => $timestamp,
			'published' => 'false'
		);

		$res = $this->api->post($facebook_page_id . '/feed/' ,$data, $pageAccessToken);
		Session::forget('fb_access_token');
		if($res->getHttpStatusCode() == 200) {
			return redirect('fb_connect_app')->with('message', 'Your Schedule Post has been created.');
		}
    }

}
