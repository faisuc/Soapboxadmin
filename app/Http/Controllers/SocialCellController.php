<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Sentinel;
use Session;
use Redirect;
use URL;
use DB;
use Facebook\Exceptions\FacebookSDKException;
use DirkGroenen\Pinterest\Pinterest;
use Facebook\Facebook;
use Laravel\Socialite\Facades\Socialite;
use Google_Client;

class SocialCellController extends Controller
{
    private $api;
    public function index()
    {
    	$this->_loadSharedViews();

        $data = [];
        
        $data['socialcells'] = $this->socialCell->orderBy('created_at', 'DESC')->get();
        
        return view('pages.social-cells', $data);
        // return view('pages.social-cells', $data);
    }

    public function add_social_cell($id='')
    {
    	$this->_loadSharedViews();
    	
    	$data = [];
		
		return view('pages.cell-create', $data);
    }


    public function store(Request $request)
    {
    	/*echo '<pre>';
    	print_r($request->input());
    	exit;*/
        $validatedData = $request->validate([
            'cellname' => 'required|min:4',
            /*'email_owner' => 'required|email|unique:social_cell,email_owner',
            'email_marketer' => 'required|email|unique:social_cell,email_marketer',
            'email_client' => 'required|email|unique:social_cell,email_client'*/
        ]);

        $cellname = $request->input('cellname');
        $email_owner = $request->input('email_owner');
        $email_marketer = $request->input('email_marketer');
        $email_client = $request->input('email_client');
        $payment_status = $request->input('payment_status');

        $socialcell = new $this->socialCell;
        $socialcell->cell_name = $cellname;
        $socialcell->email_owner = $email_owner;
        $socialcell->email_marketer	= $email_marketer;
        $socialcell->email_client = $email_client;
        $socialcell->payment_status = $payment_status;
        $socialcell->save();

		return redirect('socialcell')->with('flash_message', 'Social Cell has been Created.');
        // return redirect('/socialaccounts')->with('flash_message', 'Social account has been added.');
     
    }

    public function edit($cell_id = null)
    {
        // echo 'rt'.$cell_id;exit;
        $this->_loadSharedViews();
        
        if ($this->socialCell->find($cell_id))
        {
            $data = [];
            $data['socialcell'] = $this->socialCell->find($cell_id);
            return view('pages.cell-edit', $data);
        }
        return redirect('dashboard');
    }


    public function update(Request $request, $cell_id = null)
    {
        $validatedData = $request->validate([
            'cellname' => 'required|min:4',
            /*'email_owner' => 'required|email|unique:social_cell,email_owner,$cell_id',
            'email_marketer' => 'required|email|unique:social_cell,email_marketer,$cell_id',
            'email_client' => 'required|email|unique:social_cell,email_client,$cell_id'*/
        ]);

        $cellname = $request->input('cellname');
        $email_owner = $request->input('email_owner');
        $email_marketer = $request->input('email_marketer');
        $email_client = $request->input('email_client');
        $payment_status = $request->input('payment_status');

        $socialcell = $this->socialCell->find($cell_id);
        $socialcell->cell_name = $cellname;
        $socialcell->email_owner = $email_owner;
        $socialcell->email_marketer = $email_marketer;
        $socialcell->email_client = $email_client;
        $socialcell->payment_status = $payment_status;
        $socialcell->save();

        return redirect('socialcell/edit/'.$cell_id)->with('flash_message', 'Social Cell has been Updated.');

    }

    public function delete($cell_id = null)
    {
        $this->socialCell->find($cell_id)->delete();
        return redirect()->back()->with('flash_message', 'Social Cell has been deleted.');

    }

    public function social_cell_accounts($cell_id)
    {
        $this->_loadSharedViews();
        
        $data = [];

        $social_cell = $this->socialCell->where('id',$cell_id)->get();
        $data['social_cell'] = $social_cell[0];
        $data['social_accounts'] = $this->socialAccount->where('social_cell_id',$cell_id)->get();
        
        return view('pages.cell-account-create', $data);
    } 

    public function add_social_cell_account(Request $request)
    {
        $name = $request->input('name');
        $url = $request->input('url');
        $type_id = $request->input('social_account');
        $user_id = $request->input('user_id');
        $social_cell_id = $request->input('social_cell_id');

        if (!$user_id) {
            $user_id = Sentinel::getUser()->id;
        }

        $social = new $this->socialAccount;
        $social->type_id = $type_id;
        $social->user_id = $user_id;
        $social->social_cell_id = $social_cell_id;
        $social->name = $name;
        $social->url = $url;
        $social->save();
        
        $social_id = $social->id;
        
        if( $type_id == 4 ) {
            return redirect('redirect_google');
        }
        if( $type_id == 1 ) {
            
            // $this->fb_connect_app();
            $this->setFacebookObject();
            $helper = $this->api->getRedirectLoginHelper();
            $permissions = ['email','user_posts','manage_pages','publish_pages'];
            $helper->getPersistentDataHandler()->set('state', 'social_id='.$social_id);
            $loginUrl = $helper->getLoginUrl(URL::to('/').'/fb_cell_callback', $permissions);
            echo $loginUrl; die();
            return redirect()->away($loginUrl);
            /*if(session()->get('fb_access_token') == '')
            {
                $helper = $this->api->getRedirectLoginHelper();
                $permissions = ['email','user_posts','manage_pages','publish_pages'];
                $loginUrl = $helper->getLoginUrl(URL::to('/').'/fb_callback', $permissions);
                // echo $loginUrl; die();
                return redirect()->away($loginUrl);
                // echo "Not Redirecting. Error Occur"; die();
            }*/
            // return redirect()->back()->with('flash_message', 'Social account has been added.');
        }
        if($type_id == 3) {
            $url = 'https://api.twitter.com/oauth/request_token';
            $callback_url = getenv('TWITTER_REDIRECT').'?social_id='.$social_id;

            // $callback_url = getenv('TWITTER_REDIRECT');
            $consumer_key = getenv('TWITTER_CLIENT_ID');
            $consumer_secret = getenv('TWITTER_CLIENT_SECRET');

            $nonce = base64_encode(uniqid());
            $nonce = preg_replace('~[\W]~','',$nonce);

            $data = array(
                'oauth_callback' => $callback_url,
                'oauth_consumer_key' => $consumer_key,
                'oauth_nonce' => $nonce,
                'oauth_signature_method' => 'HMAC-SHA1',
                'oauth_timestamp' => time(),
                'oauth_version' => '1.0'
            );

            $base_string = $this->BaseString($url, $data, "POST");

            $composite_key = rawurlencode($consumer_secret) . '&';

            $oauth_signature = base64_encode(hash_hmac('sha1', $base_string, $composite_key, true));

            $data['oauth_signature'] = $oauth_signature;

            $response = $this->RequestToken($data, $url, 1);
            
            // header("location: https://api.twitter.com/oauth/authorize?$response");
            // $twitter_url = 'https://api.twitter.com/oauth/authorize?'.$response;
            $twitter_url = 'https://api.twitter.com/oauth/authorize?'.$response;
            return redirect()->away($twitter_url);
        }
        if($type_id == 5) {
            /*if(session()->get('instagram') == '') {
                session()->put('instagram',$social_id);
            }*/
            $social_new = $this->socialAccount->find($social_id);
            $social_new->instagram_user = $request->input('insta_user');
            $social_new->instagram_password = $request->input('insta_pass');
            $social_new->instagram_username = $request->input('insta_username');
            $social_new->save();

            return redirect()->back()->with('flash_message', 'Social account has been added.');
        }
        if($type_id == 6) {

            $app_id = getenv('PINTEREST_CLIENT_ID');
            $app_secret = getenv('PINTEREST_CLIENT_SECRET');
            // $callback_url = getenv('PINTEREST_REDIRECT').'?social_id='.$social_id;
            $callback_url = getenv('PINTEREST_REDIRECT');
            $pinterest = new Pinterest($app_id, $app_secret);
            $state = "social_id=".$social_id;
            $pinterest->auth->setState($state);
            $pinterest_url = $pinterest->auth->getLoginUrl($callback_url, array('read_public', 'write_public'));
            return redirect()->away($pinterest_url);
        }

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

    public function fb_cell_callback()
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
        $state = $_GET['state'];
        $state = explode('=', $state);
        $social_id = $state[1];

        $facebook_token = $accessToken;
        $social = $this->socialAccount->find($social_id);
        $social->facebook_token = $facebook_token;
        $social->save();
        // session()->put('fb_access_token', $accessToken);
        // echo Session::get('fb_access_token'); die();
        // header('Location: http://127.0.0.1:3000/fb_connect_app');

        /**/
        $cell_id = $social->social_cell_id;
        /**/

        $fb_connect_url = URL::to('/').'/fb_cell_connect_app/'.$cell_id;
        return redirect()->away($fb_connect_url);
    }

    public function fb_cell_connect_app($cell_id)
    {
        $this->setFacebookObject();

        return redirect('/socialcell/'.$cell_id)->with('flash_message', 'Social account has been added.');
        
    }

    function BaseString($url, $parameters, $method = null){
        if( empty($method) ){
            $method = 'GET';
        }

        $get_url = rawurlencode($url);

        $string = array();

        ksort($parameters);

        foreach ($parameters as $key => $value) {
            $string[] = "$key=" . rawurlencode($value);
        }

        return $method."&".$get_url."&".rawurlencode(implode('&', $string));
    }

    function AuthorizationOAuth($parameters){
        $oauth = '';

        foreach ($parameters as $key => $value) {
            $oauth .= $key.'="'.rawurlencode($value).'", ';
        }

        return array("Authorization: OAuth ".substr($oauth, 0,-2), 'Expect:');
    }

    function RequestToken($oauth, $url, $method){
        $header = $this->AuthorizationOAuth($oauth);

        $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

            if( $method == 1 ){
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $oauth);
            }
            
            $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }


}
