<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Sentinel;
use Session;
use Redirect;
use URL;
use DB;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;

class PostController extends Controller
{

    public function index($user_id = null)
    {

        $this->_loadSharedViews();

        $data = [];
        $data['posts'] = [];

        if ($user_id == null)
        {
            $data['posts'] = $this->post->where('user_id', Sentinel::getUser()->id)->orderBy('created_at', 'DESC')->get();
        }
        else
        {
            $data['posts'] = $this->post->where('user_id', $user_id)->get();
        }

        if (is_admin())
        {
            $data['managedClients'] = Sentinel::getUserRepository()->with('roles')->where('id', '<>', Sentinel::getUser()->id)->get();
        }
        else
        {
            $data['managedClients'] = $this->user->find(Sentinel::getUser()->id)->clients();
        }

        return view('pages.queues', $data);

    }

    public function create()
    {

        $this->_loadSharedViews();

        $this->setFacebookObject();
        $data = [];
        $token = 'EAAUKtADcetYBAGy9lPpQrMe8fODdcZCwtnNyTWb9J3MqOiCcDgOJc1r7f6kCvgTvyfb8OWyHG286DelvaLejOOTe6SuhbRPb89xYbkrPkjFNRZBnh4XaFXnZCQ42TVifHKuOGtuSHt8cTBR86zSzZA0apZCfZCGx48uZAcZAkwNjp1xUpWO0SFV9';
        // session()->put('fb_access_token',$token);
        // session()->forget('fb_access_token');
        if(session()->get('fb_access_token') != '')
        {
            $token = 'EAAUKtADcetYBAGy9lPpQrMe8fODdcZCwtnNyTWb9J3MqOiCcDgOJc1r7f6kCvgTvyfb8OWyHG286DelvaLejOOTe6SuhbRPb89xYbkrPkjFNRZBnh4XaFXnZCQ42TVifHKuOGtuSHt8cTBR86zSzZA0apZCfZCGx48uZAcZAkwNjp1xUpWO0SFV9';
            // $token = session()->get('fb_access_token');
            $userdata = $this->api->get('/me', $token);
            $userdata = $userdata->getGraphUser();
            $user_id = $userdata['id'];
            $accounts = $this->api->get('/'.$user_id.'/accounts', $token);
            
            $accounts = $accounts->getDecodedBody();
            $data['pages'] = $accounts['data'];
        }

        if(session()->get('twitter_logged_in') != '') {
            $data['twitter'] = true;
        }
        
        return view('pages.post-create', $data);

    }

    public function store(Request $request)
    {

        $title = $request->input('title');
        $description = $request->input('description');
        $link = $request->input('link');
        $schedule_date = $request->input('schedule_date');
        $user_id = $request->input('user_id');
        $status = $request->input('status');

        if (!$user_id) {
            $user_id = Sentinel::getUser()->id;
        }

        $data = [];
        $media_id = 0;

        if ($request->has('photo'))
        {

            $photo = $request->file('photo');
            $fileName = uniqid() . $photo->getClientOriginalName();
            $filePath = '/public/medias/images/' . $fileName;
            $photo->storeAs('/public/medias/images/', $fileName);
            $data['photo'] = $fileName;

            $media = new $this->media;
            $media->post_id = 0;
            $media->type_id = 1;
            $media->file_name = $fileName;
            $media->file_ext = pathinfo($filePath, PATHINFO_EXTENSION);
            $media->save();

            $media_id = $media->id;

        }

        $post = new $this->post;
        $post->user_id = $user_id;
        $post->title = $title;
        $post->description = $description;

        if ($user = Sentinel::getUser())
        {
            if ($user->inRole('client'))
            {
                $post->status = 4;
            } else {
                $post->status = 3;
            }
        }

        if ($media_id != 0)
        {
            $post->featured_image_id = $media_id;
        }

        $post->link = $link;
        $post->schedule_to_post_date = Carbon::createFromFormat('Y-m-d H:i A', $schedule_date)->toDateTimeString();
        $post->save();

        if ($media_id != 0)
        {
            $media = $this->media->find($media_id);
            $media->post_id = $post->id;
            $media->save();
        }

        /* Schedule Post Facebook Page */
        if ($request->input('fb_page') != '') {
            $page_id = $request->input('fb_page');
            $post_id = $post->id;
            $publish_post = $this->fb_publish_post($page_id,$post_id);
        }
        /* Schedule Post Facebook Page */

        if ($request->input('twitter_post') != '') {

            $callback_url = getenv('TWITTER_REDIRECT');
            $consumer_key = getenv('TWITTER_CLIENT_ID');
            $consumer_secret = getenv('TWITTER_CLIENT_SECRET');
            
            $oauth_token = session()->get('twitter_oauth_token');
            $oauth_token_secret = session()->get('twitter_oauth_token_secret');

            /* Direct POST */
            $url = 'https://api.twitter.com/1.1/statuses/update.json';
            $parameters = array('status' => $title);
            $result = $this->Request($url, 'post', $consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret, $parameters);
            /* Direct POST */

            /* Schedule POST /
            $url = 'https://ads-api.twitter.com/5/accounts/:account_id/scheduled_tweets';
            /* Schedule POST */

        }

        return redirect()->back()->with('flash_message', 'New post has been created.');

    }

    public function edit($post_id = null)
    {

        $this->_loadSharedViews();
        if ($this->post->find($post_id))
        {

            $data = [];

            if (is_client())
            {
                $can_edit = $this->post->where('user_id', Sentinel::getUser()->id)->where('id', $post_id)->first();

                if ( ! $can_edit)
                {
                    return redirect('dashboard');
                }

            }

            $data['post'] = $this->post->find($post_id);
            $this->setFacebookObject();
            $token = 'EAAUKtADcetYBAGy9lPpQrMe8fODdcZCwtnNyTWb9J3MqOiCcDgOJc1r7f6kCvgTvyfb8OWyHG286DelvaLejOOTe6SuhbRPb89xYbkrPkjFNRZBnh4XaFXnZCQ42TVifHKuOGtuSHt8cTBR86zSzZA0apZCfZCGx48uZAcZAkwNjp1xUpWO0SFV9';
            // session()->put('fb_access_token',$token);
            // session()->forget('fb_access_token');
            if(session()->get('fb_access_token') != '')
            {
                $token = 'EAAUKtADcetYBAGy9lPpQrMe8fODdcZCwtnNyTWb9J3MqOiCcDgOJc1r7f6kCvgTvyfb8OWyHG286DelvaLejOOTe6SuhbRPb89xYbkrPkjFNRZBnh4XaFXnZCQ42TVifHKuOGtuSHt8cTBR86zSzZA0apZCfZCGx48uZAcZAkwNjp1xUpWO0SFV9';
                // $token = session()->get('fb_access_token');
                $userdata = $this->api->get('/me', $token);
                $userdata = $userdata->getGraphUser();
                $user_id = $userdata['id'];
                $accounts = $this->api->get('/'.$user_id.'/accounts', $token);
                
                $accounts = $accounts->getDecodedBody();
                $data['pages'] = $accounts['data'];
            }

            if(session()->get('twitter_logged_in') != '') {
                $data['twitter'] = true;
            }

            return view('pages.post-edit', $data);
        }

        return redirect('dashboard');

    }

    public function update(Request $request, $post_id = null)
    {

        $title = $request->input('title');
        $description = $request->input('description');
        $link = $request->input('link');
        $schedule_date = $request->input('schedule_date');
        $status = $request->input('status');

        $data = [];
        $media_id = 0;

        if ($request->has('photo'))
        {

            $photo = $request->file('photo');
            $fileName = uniqid() . $photo->getClientOriginalName();
            $filePath = '/public/medias/images/' . $fileName;
            $photo->storeAs('/public/medias/images/', $fileName);
            $data['photo'] = $fileName;

            $media = new $this->media;
            $media->post_id = 0;
            $media->type_id = 1;
            $media->file_name = $fileName;
            $media->file_ext = pathinfo($filePath, PATHINFO_EXTENSION);
            $media->save();

            $media_id = $media->id;

        }

        $post = $this->post->find($post_id);
        $post->title = $title;
        $post->description = $description;
        $post->status = $status;

        if ($media_id != 0)
        {
            $post->featured_image_id = $media_id;
        }

        $post->link = $link;
        $post->schedule_to_post_date = Carbon::createFromFormat('Y-m-d H:i A', $schedule_date)->toDateTimeString();
        $post->save();

        if ($media_id != 0)
        {
            $media = $this->media->find($media_id);
            $media->post_id = $post_id;
            $media->save();
        }

        /* Schedule Post Facebook Page */
        if ($request->input('fb_page') != '') {
            $page_id = $request->input('fb_page');
            $post_id = $post->id;
            $publish_post = $this->fb_publish_post($page_id,$post_id);
        }
        /* Schedule Post Facebook Page */

        if ($request->input('twitter_post') != '') {

            $callback_url = getenv('TWITTER_REDIRECT');
            $consumer_key = getenv('TWITTER_CLIENT_ID');
            $consumer_secret = getenv('TWITTER_CLIENT_SECRET');
            
            $oauth_token = session()->get('twitter_oauth_token');
            $oauth_token_secret = session()->get('twitter_oauth_token_secret');

            /* Direct POST */
            $url = 'https://api.twitter.com/1.1/statuses/update.json';
            $parameters = array('status' => $title);
            $result = $this->Request($url, 'post', $consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret, $parameters);
            /* Direct POST */

            /* Schedule POST /
            $parameters = array('status' => $title);
            $account_url = 'https://ads-api.twitter.com/5/accounts';
            $accounts = $this->Request($account_url, 'get', $consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret, array());
            // $url = 'https://ads-api.twitter.com/5/accounts/'.$account_id.'/scheduled_tweets';
            /* Schedule POST */
        }

        return redirect()->back()->with('flash_message', 'Post has been updated.');

    }

    public function delete($post_id = null)
    {

        $this->post->find($post_id)->delete();

        return redirect()->back()->with('flash_message', 'Post has been deleted.');

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
        // $fb_connect_url = URL::to('/').'/fb_publish_post';
        $fb_connect_url = URL::to('/').'/queues';
        return redirect()->away($fb_connect_url);
    }

    public function deauthorize_fb_app()
    {
        echo "string";
    }

    public function destroy_session_fb_app()
    {
        // session_destroy();
        session()->forget('fb_access_token');
    }

    public function display_pages($post_id=null,$user_id=null)
    {
        $this->_loadSharedViews();

        $this->setFacebookObject();
        if(session()->get('fb_access_token') == '')
        {
            $helper = $this->api->getRedirectLoginHelper();
            $permissions = ['email','user_posts','manage_pages','publish_pages'];
            $loginUrl = $helper->getLoginUrl(URL::to('/').'/fb_oauth_callback', $permissions);
            echo $loginUrl; die();
            return redirect()->away($loginUrl);
            echo "Not Redirecting. Error Occur"; die();
        }

        $token = session()->get('fb_access_token');

        $userdata = $this->api->get('/me', $token);
        $userdata = $userdata->getGraphUser();
        $user_id = $userdata['id'];
        $accounts = $this->api->get('/'.$user_id.'/accounts', $token);
        // $permissions = $this->api->get('/'.$user_id.'/permissions', $token);
        
        $data['posts'] = [];
        
        if ($user_id == null)
        {
            $data['posts'] = $this->post->where('user_id', Sentinel::getUser()->id)->orderBy('created_at', 'DESC')->get();
        }
        else
        {
            $data['posts'] = $this->post->where('user_id', $user_id)->get();
        }

        if (is_admin())
        {
            $data['managedClients'] = Sentinel::getUserRepository()->with('roles')->where('id', '<>', Sentinel::getUser()->id)->get();
        }
        else
        {
            $data['managedClients'] = $this->user->find(Sentinel::getUser()->id)->clients();
        }

        $data['fb_manage_pages'] = true;
        $accounts = $accounts->getDecodedBody();
        $data['pages'] = $accounts['data'];
        $data['post_id'] = $post_id;

        return view('pages.queues', $data);
    }

    public function fb_publish_post($page_id,$post_id)
    {
        $response = array();
        $this->setFacebookObject();
        
        $token = session()->get('fb_access_token');
        $userdata = $this->api->get('/me', $token);
        $userdata = $userdata->getGraphUser();
        $user_id = $userdata['id'];
        $accounts = $this->api->get('/'.$user_id.'/accounts', $token);
        // $permissions = $this->api->get('/'.$user_id.'/permissions', $token);
        $accounts = $accounts->getDecodedBody();
        
        $facebook_page_id = '';
        foreach ($accounts['data'] as $page_key => $page) {
            if($page['id'] == $page_id) {
                $pageAccessToken = $page['access_token'];
                $facebook_page_id = $page['id'];
            }
        }

        $post_details = $this->post->where('user_id', Sentinel::getUser()->id)->where('id', $post_id)->first();
        $post = $this->post->find($post_id);
        
        $title = $post->title;
        $description = $post->description;
        $image = $post->featured_image_id;
        $link = $post->link;
        $status = $post->status;
        $schedule = $post->schedule_to_post_date;

        if($facebook_page_id != '') {
            // date_default_timezone_set('Asia/Kolkata');
            // $message = 'scheduled post my script new script';
            // echo $current_time.'--'.$timestamp; die();
            // echo $token; die();
            $timestamp = $schedule;
            $timestamp = strtotime($timestamp);

            $data = array(
                'message' => $title,
                // 'description' => $description,
                // 'link' => $link,
                'scheduled_publish_time' => $timestamp,
                'published' => 'false'
            );

            $res = $this->api->post($facebook_page_id . '/feed/' ,$data, $pageAccessToken);
            // session()->forget('fb_access_token');
            if($res->getHttpStatusCode() == 200) {
                $response['success'] = true;
                $response['message'] = "Your Schedule Post has been posted.";
                // return redirect('/queues')->with('flash_message', 'Your Schedule Post has been posted.');
            }
        }
        else {
            $response['success'] = true;
            $response['message'] = "Please Create a Page on Your Facebook Account.";
            // return redirect('fb_connect_app')->with('flash_message', 'Please Create a Page on Your Facebook Account.');
        }

        return $response;
    }

    public function removeAccess()
    {
        $token = session()->get('fb_access_token');

        $response = $this->api->get('/me?fields=id,name', $token);
        $user_profile = $response->getGraphUser();
        $user_id = $user_profile['id'];
        try {
            // Returns a `Facebook\FacebookResponse` object
            $response = $this->api->get(
                '/'.$user_id.'/permissions',
                $token
            );
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        $graphNode = $response->getGraphNode();
        
    }

    /* Twitter */
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
            echo $get_url; die();
            $result = file_get_contents($get_url);        
            $json = json_decode($result, true);
            return $json;
        }
    }
    /* Twitter */

}
