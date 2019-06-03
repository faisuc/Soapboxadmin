<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Sentinel;
use Session;
use Redirect;
use URL;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Laravel\Socialite\Facades\Socialite;
use Google_Client;

class SocialAccountController extends Controller
{
    private $api;
    public function index($user_id = null)
    {

        $this->_loadSharedViews();

        $data = [];
        $data['socials'] = [];

        if ($user_id == null)
        {
            $data['socials'] = $this->socialAccount->where('user_id', Sentinel::getUser()->id)->orderBy('created_at', 'DESC')->get();
        }
        else
        {
            $data['socials'] = $this->socialAccount->where('user_id', $user_id)->get();
        }

        if (is_admin())
        {
            $data['managedClients'] = Sentinel::getUserRepository()->with('roles')->where('id', '<>', Sentinel::getUser()->id)->get();
        }
        else
        {
            $data['managedClients'] = $this->user->find(Sentinel::getUser()->id)->clients();
        }

        $user_id = $user_id ? $user_id : Sentinel::getUser()->id;

        return view('pages.social-accounts', $data);

    }

    public function store(Request $request)
    {
        

        $name = $request->input('name');
        $url = $request->input('url');
        $type_id = $request->input('social_account');
        $user_id = $request->input('user_id');

        if (!$user_id) {
            $user_id = Sentinel::getUser()->id;
        }

        
        $social = new $this->socialAccount;
        $social->type_id = $type_id;
        $social->user_id = $user_id;
        $social->name = $name;
        $social->url = $url;
        $social->save();
        
        if( $type_id == 4 ) {
            return redirect('redirect_google');
        }
        if( $type_id == 1 ) {
            // $this->fb_connect_app();
            $this->setFacebookObject();
            if(session()->get('fb_access_token') == '')
            {
                $helper = $this->api->getRedirectLoginHelper();
                $permissions = ['email','user_posts','manage_pages','publish_pages'];
                $loginUrl = $helper->getLoginUrl(URL::to('/').'/fb_callback', $permissions);
                // echo $loginUrl; die();
                return redirect()->away($loginUrl);
                // echo "Not Redirecting. Error Occur"; die();
            }
            return redirect()->back()->with('flash_message', 'Social account has been added.');
        }
        if($type_id == 3) {
            session()->forget('twitter_logged_in');
            if(session()->get('twitter_logged_in') == '')
            {
                $url = 'https://api.twitter.com/oauth/request_token';
                $callback_url = getenv('TWITTER_REDIRECT');
                $consumer_key = getenv('TWITTER_CLIENT_ID');
                $consumer_secret = getenv('TWITTER_CLIENT_SECRET');

                $data = array(
                    'oauth_callback' => $callback_url,
                    'oauth_consumer_key' => $consumer_key,
                    'oauth_nonce' => time(),
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

        }

    }

    public function delete($social_id = null)
    {

        if ($social_id)
        {

            $this->socialAccount->destroy($social_id);

            return redirect()->back()->with('flash_message', 'Social account has been unlinked.');

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

    public function fb_connect_app()
    {
        $this->setFacebookObject();

        // session()->flush();
        // echo 'Token: '.session()->get('fb_access_token'); die();
        /*if(session()->get('fb_access_token') == '')
        {
            $helper = $this->api->getRedirectLoginHelper();
            $permissions = ['email','user_posts','manage_pages','publish_pages'];
            $loginUrl = $helper->getLoginUrl(URL::to('/').'/fb_callback', $permissions);
            echo $loginUrl; die();
            return redirect()->away($loginUrl);
            echo "Not Redirecting. Error Occur"; die();
        }*/

        return redirect('/socialaccounts')->with('flash_message', 'Social account has been added.');
        
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
        session()->put('fb_access_token', $accessToken);
        // echo Session::get('fb_access_token'); die();
        // header('Location: http://127.0.0.1:3000/fb_connect_app');
        $fb_connect_url = URL::to('/').'/fb_connect_app';
        return redirect()->away($fb_connect_url);
    }




    /* Google login */
    public function redirectToProvider()
    {
        return Socialite::driver('google')->scopes(['https://www.googleapis.com/auth/plus.business.manage'])->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback()
    {
        try {
            $user = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect('/login');
        }

        // only allow people with @company.com to login
        echo "<pre>";
        print_r($user);
        die();
        
        /*if(explode("@", $user->email)[1] !== 'company.com'){
            return redirect()->to('/');
        }*/
        // check if they're an existing user
       
        // $existingUser = User::where('email', $user->email)->first();
        // if($existingUser){
        //     // log them in
        //     auth()->login($existingUser, true);
        // } else {
        //     // create a new user
        //     $newUser                  = new User;
        //     $newUser->name            = $user->name;
        //     $newUser->email           = $user->email;
        //     $newUser->google_id       = $user->id;
        //     $newUser->avatar          = $user->avatar;
        //     $newUser->avatar_original = $user->avatar_original;
        //     $newUser->save();
        //     auth()->login($newUser, true);
        // }
        // return redirect('/socialaccounts')->with('flash_message', 'Google Social account has been added. Welcome '.$user->email);
        //return redirect()->to('/home');
    }

    /* Google login */

    public function create_google_post()
    {
        $user = Socialite::driver('google')->user();
        echo "<pre>";
        print_r($user);
        die();
        $google_client_token = [
            'access_token' => $user->token,
            'refresh_token' => $user->refreshToken,
            'expires_in' => $user->expiresIn
        ];

        $client = new Google_Client();
        $client->setApplicationName("Laravel");
        $client->setDeveloperKey(env('GOOGLE_SERVER_KEY'));
        $client->setAccessToken(json_encode($google_client_token));
    }

    /* Twitter */
    public function setTwitterObject()
    {
        $url = 'https://api.twitter.com/oauth/request_token';
        $callback_url = getenv('TWITTER_REDIRECT');
        $consumer_key = getenv('TWITTER_CLIENT_ID');
        $consumer_secret = getenv('TWITTER_CLIENT_SECRET');

        $data = array(
            'oauth_callback' => $callback_url,
            'oauth_consumer_key' => $consumer_key,
            'oauth_nonce' => time(),
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
        $twitter_url = 'https://api.twitter.com/oauth/authorize?'.$response;
        echo $twitter_url;
        return redirect()->away($twitter_url);
    }

    public function twitter_callback()
    {
        if( isset($_GET['oauth_token']) and isset($_GET['oauth_verifier']) and (session()->get('twitter_logged_in') == '') ){
            $oauth_token = $_GET['oauth_token'];
            $oauth_verifier = $_GET['oauth_verifier'];

            $get_data = file_get_contents("https://api.twitter.com/oauth/access_token?oauth_token=$oauth_token&oauth_verifier=$oauth_verifier");
            $array = explode("&", $get_data);

            session()->put('twitter_oauth_token', str_replace("oauth_token=", NULL, $array[0]));
            session()->put('twitter_oauth_token_secret', str_replace("oauth_token_secret=", NULL, $array[1]));
            session()->put('twitter_user_id', str_replace("user_id=", NULL, $array[2]));
            session()->put('twitter_screen_name', str_replace("screen_name=", NULL, $array[3]));
            
            session()->put('twitter_logged_in', 1);
            
            return redirect('/socialaccounts')->with('flash_message', 'Social account has been added.');
        }else{
            return redirect('/socialaccounts')->with('flash_message', 'Social account has been added.');
        }
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


    function Request($url, $method, $key, $key_secret, $token, $token_secret, $parameters){
        $param = array(
                    'oauth_consumer_key' => $key,
                    'oauth_nonce' => time(),
                    'oauth_signature_method' => 'HMAC-SHA1',
                    'oauth_timestamp' => time(),
                    'oauth_token' => $token,
                    'oauth_version' => '1.0',
                );

        if( strtolower($method) == 'post' ){
            $get_method = 'POST';
        }elseif( strtolower($method) == 'delete' ){
            $get_method = 'DELETE';
        }else{
            $get_method = 'GET';
        }

        if( !empty($parameters) ){
            $array_merge = array_merge($param, $parameters);
            $the_param = $array_merge;
        }else{
            $the_param = $param;
        }

        $base_string = $this->BaseString($url, $the_param, $get_method);

        $composite_key = $key_secret."&".$token_secret;

        $oauth_signature = base64_encode(hash_hmac('sha1', $base_string, $composite_key, true));

        $the_param['oauth_signature'] = $oauth_signature;

        if( $get_method == 'POST' or $get_method == "DELETE" ){
            $options = array('http' =>
                            array(
                                'method'  => $get_method,
                                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                                'content'  => http_build_query($the_param)
                            )
                        );

            $context  = stream_context_create($options);

            $result = file_get_contents($url, false, $context);

            $json = json_decode($result, true);

            return $json;
        }

        else{
            $get_url = $url."?".http_build_query($the_param);
            $result = file_get_contents($get_url);        
            $json = json_decode($result, true);
            return $json;
        }
    }
    /* Twitter */
}
