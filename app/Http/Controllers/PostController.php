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
use DirkGroenen\Pinterest\Pinterest;
use Facebook\Facebook;

class PostController extends Controller
{
    private $image; 
    private $image_type;
    public $username;
    public $password;
    private $guid;
    private $userAgent = 'Instagram 6.21.2 Android (19/4.4.2; 480dpi; 1152x1920; Meizu; MX4; mx4; mt6595; en_US)';
    private $instaSignature ='25eace5393646842f0d0c3fb2ac7d3cfa15c052436ee86b5406a8433f54d24a5';
    private $instagramUrl = 'https://i.instagram.com/api/v1/';

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
        // $social_account = $this->socialAccount->where('user_id', Sentinel::getUser()->id)->orderBy('created_at', 'DESC')->get()->first();

        /*if(session()->get('fb_access_token') != '')
        {
            $token = session()->get('fb_access_token');
            $userdata = $this->api->get('/me', $token);
            $userdata = $userdata->getGraphUser();
            $user_id = $userdata['id'];
            $accounts = $this->api->get('/'.$user_id.'/accounts', $token);
            
            $accounts = $accounts->getDecodedBody();
            $data['pages'] = $accounts['data'];
        }*/

        /*if(session()->get('twitter_logged_in') != '') {
            $data['twitter'] = true;
        }*/
        $facebook_account = $this->socialAccount->where('user_id', Sentinel::getUser()->id)->where('type_id', 1)->where('deleted_at', NULL)->orderBy('created_at', 'DESC')->get()->first();
        $twitter_account = $this->socialAccount->where('user_id', Sentinel::getUser()->id)->where('type_id', 3)->where('deleted_at', NULL)->orderBy('created_at', 'DESC')->get()->first();
        $instagram_account = $this->socialAccount->where('user_id', Sentinel::getUser()->id)->where('type_id', 5)->where('deleted_at', NULL)->orderBy('created_at', 'DESC')->get()->first();
        $pinterest_account = $this->socialAccount->where('user_id', Sentinel::getUser()->id)->where('type_id', 6)->where('deleted_at', NULL)->orderBy('created_at', 'DESC')->get()->first();
        if(!empty($facebook_account)) {
            if($facebook_account->facebook_token) {
                $token = $facebook_account->facebook_token;
                $userdata = $this->api->get('/me', $token);
                $userdata = $userdata->getGraphUser();
                $user_id = $userdata['id'];
                $accounts = $this->api->get('/'.$user_id.'/accounts', $token);
                
                $accounts = $accounts->getDecodedBody();
                $data['pages'] = $accounts['data'];
                $data['facebook'] = true;
            }
        }
        if (!empty($twitter_account)) {
            if($twitter_account->twitter_session && $twitter_account->twitter_secret) {
                $data['twitter'] = true;
            }
        }
        if (!empty($instagram_account)) {
            $data['instagram'] = true;
        }
        if (!empty($pinterest_account)) {
            if($pinterest_account->pinterest_token) {
                $token = $pinterest_account->pinterest_token;

                $app_id = getenv('PINTEREST_CLIENT_ID');
                $app_secret = getenv('PINTEREST_CLIENT_SECRET');
                $callback_url = getenv('PINTEREST_REDIRECT');
                $pinterest = new Pinterest($app_id, $app_secret);
                $pinterest->auth->setOAuthToken($token);

                /* Get User Boards */
                $boards = $pinterest->users->getMeBoards();
                $boardsArr = array();
                foreach ($boards as $board_key => $board) {
                    $board_id = $board->id;
                    $boardsArr[$board_id] = $board->name;
                }

                $data['boards'] = $boardsArr;
                $data['pinterest'] = true;
            }
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
        if ($request->input('facebook_post') != '') {
            $page_id = $request->input('fb_page');
            $post_id = $post->id;
            $publish_post = $this->fb_publish_post($page_id,$post_id);
        }
        /* Schedule Post Facebook Page */

        if ($request->input('twitter_post') != '') {

            $post_id = $post->id;
            $callback_url = getenv('TWITTER_REDIRECT');
            $consumer_key = getenv('TWITTER_CLIENT_ID');
            $consumer_secret = getenv('TWITTER_CLIENT_SECRET');
            
            /*$oauth_token = session()->get('twitter_oauth_token');
            $oauth_token_secret = session()->get('twitter_oauth_token_secret');*/
            $social_account = $this->socialAccount->where('user_id', Sentinel::getUser()->id)->where('type_id', 3)->where('deleted_at', NULL)->orderBy('created_at', 'DESC')->get()->first();
            $oauth_token = $social_account->twitter_session;
            $oauth_token_secret = $social_account->twitter_secret;

            $post_date = date('Y-m-d H:i:s',strtotime($schedule_date));
            $data = array('post_id'=>$post_id,'type_name'=>'twitter','session'=>$oauth_token,'session_secret'=>$oauth_token_secret,'post_date'=>$post_date,'is_cron_run'=>0);
            DB::table('cron_script')->insert($data);

            /* Direct POST /
            $url = 'https://api.twitter.com/1.1/statuses/update.json';
            $parameters = array('status' => $title);
            $result = $this->Request($url, 'post', $consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret, $parameters);
            /* Direct POST */

            /* Schedule POST /
            $url = 'https://ads-api.twitter.com/5/accounts/:account_id/scheduled_tweets';
            /* Schedule POST */

        }

        if($request->input('instagram_post') != '') {
            // $social_id = session()->get('instagram');
            
            $social_account = $this->socialAccount->where('user_id', Sentinel::getUser()->id)->where('type_id', 5)->where('deleted_at', NULL)->orderBy('created_at', 'DESC')->get()->first();
            $insta_user = $social_account->instagram_user;
            $insta_pass = $social_account->instagram_password;
            $username = $insta_user;
            $password = $insta_pass;
            $oauth_token = $username;
            $oauth_token_secret = $password;
            
            /*$insta_user = $request->input('insta_username');
            $insta_pass = $request->input('insta_password');*/

            $filename = $post->featured_image;

            $caption = $title;
            $schedule = $schedule_date;
            date_default_timezone_set('Asia/Kolkata');
            $schedule = strtotime($schedule);

            // $new_filename = url($filename);

            $post_date = date('Y-m-d H:i:s',strtotime($schedule_date));
            $data = array('post_id'=>$post_id,'type_name'=>'instagram','session'=>$oauth_token,'session_secret'=>$oauth_token_secret,'post_date'=>$post_date,'is_cron_run'=>0);
            DB::table('cron_script')->insert($data);

            /*$root = $_SERVER['DOCUMENT_ROOT'];
            if($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
                $new_filename = $root.$filename;
            }
            else {
                $request_uri = '/public/';
                $new_filename = $root.$request_uri.$filename;
            }

            $this->image_load($new_filename);
            $this->image_resize(480,600);
            $this->image_save($new_filename, IMAGETYPE_JPEG);

            $response = $this->insta_login($username, $password);

            if(strpos($response[1], "Sorry")) {
                echo "Request failed, there's a chance that this proxy/ip is blocked";
                print_r($response);
                exit();
            }         
            if(empty($response[1])) {
                echo "Empty response received from the server while trying to login";
                print_r($response); 
                exit(); 
            }

            $insta_post = $this->insta_post($new_filename, $caption, $schedule);*/
            /*echo "<pre>";
            print_r($response);
            die();*/
        }
        if ($request->input('pinterest_post') != '') {

            $board_id = $request->input('pint_board');
            $social_account = $this->socialAccount->where('user_id', Sentinel::getUser()->id)->where('type_id', 6)->where('deleted_at', NULL)->orderBy('created_at', 'DESC')->get()->first();
            
            $token = $social_account->pinterest_token;
            
            $schedule = $schedule_date;
            date_default_timezone_set('Asia/Kolkata');
            $schedule = strtotime($schedule);

            $post_date = date('Y-m-d H:i:s',strtotime($schedule_date));
            $data = array('post_id'=>$post_id,'type_name'=>'pinterest','session'=>$token,'session_secret'=>$board_id,'post_date'=>$post_date,'is_cron_run'=>0);
            DB::table('cron_script')->insert($data);
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
            
            // $social_account = $this->socialAccount->where('user_id', Sentinel::getUser()->id)->orderBy('created_at', 'DESC')->get()->first();

            /*if(session()->get('fb_access_token') != '')
            {
                $token = session()->get('fb_access_token');
                $userdata = $this->api->get('/me', $token);
                $userdata = $userdata->getGraphUser();
                $user_id = $userdata['id'];
                $accounts = $this->api->get('/'.$user_id.'/accounts', $token);
                
                $accounts = $accounts->getDecodedBody();
                $data['pages'] = $accounts['data'];
            }*/

            /*if(session()->get('twitter_logged_in') != '') {
                $data['twitter'] = true;
            }*/
            $facebook_account = $this->socialAccount->where('user_id', Sentinel::getUser()->id)->where('type_id', 1)->where('deleted_at', NULL)->orderBy('created_at', 'DESC')->get()->first();
            $twitter_account = $this->socialAccount->where('user_id', Sentinel::getUser()->id)->where('type_id', 3)->where('deleted_at', NULL)->orderBy('created_at', 'DESC')->get()->first();
            $instagram_account = $this->socialAccount->where('user_id', Sentinel::getUser()->id)->where('type_id', 5)->where('deleted_at', NULL)->orderBy('created_at', 'DESC')->get()->first();
            $pinterest_account = $this->socialAccount->where('user_id', Sentinel::getUser()->id)->where('type_id', 6)->where('deleted_at', NULL)->orderBy('created_at', 'DESC')->get()->first();
            if(!empty($facebook_account)) {
                if($facebook_account->facebook_token) {
                    $token = $facebook_account->facebook_token;
                    $userdata = $this->api->get('/me', $token);
                    $userdata = $userdata->getGraphUser();
                    $user_id = $userdata['id'];
                    $accounts = $this->api->get('/'.$user_id.'/accounts', $token);
                    
                    $accounts = $accounts->getDecodedBody();
                    $data['pages'] = $accounts['data'];
                    $data['facebook'] = true;
                }
            }
            if(!empty($twitter_account)) {
                if($twitter_account->twitter_session && $twitter_account->twitter_secret) {
                    $data['twitter'] = true;
                }
            }
            if (!empty($instagram_account)) {
                $data['instagram'] = true;
            }
            if (!empty($pinterest_account)) {
                if($pinterest_account->pinterest_token) {
                    $token = $pinterest_account->pinterest_token;

                    $app_id = getenv('PINTEREST_CLIENT_ID');
                    $app_secret = getenv('PINTEREST_CLIENT_SECRET');
                    $callback_url = getenv('PINTEREST_REDIRECT');
                    $pinterest = new Pinterest($app_id, $app_secret);
                    echo "string"; die();
                    $pinterest->auth->setOAuthToken($token);

                    /* Get User Boards */
                    $boards = $pinterest->users->getMeBoards();
                    $boardsArr = array();
                    foreach ($boards as $board_key => $board) {
                        $board_id = $board->id;
                        $boardsArr[$board_id] = $board->name;
                    }

                    $data['boards'] = $boardsArr;
                    $data['pinterest'] = true;
                }
            }
            
            /*if(session()->get('instagram')) {
                $data['instagram'] = true;
            }*/

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
        if ($request->input('facebook_post') != '') {
            $page_id = $request->input('fb_page');
            $post_id = $post->id;
            $publish_post = $this->fb_publish_post($page_id,$post_id);
        }
        /* Schedule Post Facebook Page */

        if ($request->input('twitter_post') != '') {

            $post_id = $post->id;
            $callback_url = getenv('TWITTER_REDIRECT');
            $consumer_key = getenv('TWITTER_CLIENT_ID');
            $consumer_secret = getenv('TWITTER_CLIENT_SECRET');
            
            /*$oauth_token = session()->get('twitter_oauth_token');
            $oauth_token_secret = session()->get('twitter_oauth_token_secret');*/
            $social_account = $this->socialAccount->where('user_id', Sentinel::getUser()->id)->where('type_id', 3)->where('deleted_at', NULL)->orderBy('created_at', 'DESC')->get()->first();
            $oauth_token = $social_account->twitter_session;
            $oauth_token_secret = $social_account->twitter_secret;

            $post_date = date('Y-m-d H:i:s',strtotime($schedule_date));
            $data = array('post_id'=>$post_id,'type_name'=>'twitter','session'=>$oauth_token,'session_secret'=>$oauth_token_secret,'post_date'=>$post_date,'is_cron_run'=>0);
            DB::table('cron_script')->insert($data);
            
            /* Direct POST /
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

        if($request->input('instagram_post') != '') {
            // $social_id = session()->get('instagram');
            $social_account = $this->socialAccount->where('user_id', Sentinel::getUser()->id)->where('type_id', 5)->where('deleted_at', NULL)->orderBy('created_at', 'DESC')->get()->first();
            
            $insta_user = $social_account->instagram_user;
            $insta_pass = $social_account->instagram_password;
            $username = $insta_user;
            $password = $insta_pass;
            $oauth_token = $username;
            $oauth_token_secret = $password;
            
            /*$insta_user = $request->input('insta_username');
            $insta_pass = $request->input('insta_password');*/
            
            $filename = $post->featured_image;

            $caption = $title;
            $schedule = $schedule_date;
            date_default_timezone_set('Asia/Kolkata');
            $schedule = strtotime($schedule);


            $post_date = date('Y-m-d H:i:s',strtotime($schedule_date));
            $data = array('post_id'=>$post_id,'type_name'=>'instagram','session'=>$oauth_token,'session_secret'=>$oauth_token_secret,'post_date'=>$post_date,'is_cron_run'=>0);
            DB::table('cron_script')->insert($data);

            // $new_filename = url($filename);
            /*$root = $_SERVER['DOCUMENT_ROOT'];
            if($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
                $new_filename = $root.$filename;
            }
            else {
                $request_uri = '/public/';
                $new_filename = $root.$request_uri.$filename;
            }

            $this->image_load($new_filename);
            $this->image_resize(480,600);
            $this->image_save($new_filename, IMAGETYPE_JPEG);

            $response = $this->insta_login($username, $password);

            if(strpos($response[1], "Sorry")) {
                echo "Request failed, there's a chance that this proxy/ip is blocked";
                print_r($response);
                exit();
            }         
            if(empty($response[1])) {
                echo "Empty response received from the server while trying to login";
                print_r($response); 
                exit(); 
            }

            $insta_post = $this->insta_post($new_filename, $caption, $schedule);*/
            /*echo "<pre>";
            print_r($response);
            die();*/
        }

        if ($request->input('pinterest_post') != '') {

            $board_id = $request->input('pint_board');
            $social_account = $this->socialAccount->where('user_id', Sentinel::getUser()->id)->where('type_id', 6)->where('deleted_at', NULL)->orderBy('created_at', 'DESC')->get()->first();
            
            $token = $social_account->pinterest_token;
            
            /*$filename = $post->featured_image;
            $root = $_SERVER['DOCUMENT_ROOT'];
            if($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
                $image = $root.$filename;
            }
            else {
                $request_uri = '/public/';
                $image = $root.$request_uri.$filename;
            }

            $app_id = getenv('PINTEREST_CLIENT_ID');
            $app_secret = getenv('PINTEREST_CLIENT_SECRET');
            $callback_url = getenv('PINTEREST_REDIRECT');
            $pinterest = new Pinterest($app_id, $app_secret);
            $pinterest->auth->setOAuthToken($token);
            $caption = $title;

            $pin = $pinterest->pins->create(array(
                "note"          => $caption,
                "image"     => $image,
                "board"         => $board_id
            ));*/

            $schedule = $schedule_date;
            date_default_timezone_set('Asia/Kolkata');
            $schedule = strtotime($schedule);

            $post_date = date('Y-m-d H:i:s',strtotime($schedule_date));
            $data = array('post_id'=>$post_id,'type_name'=>'pinterest','session'=>$token,'session_secret'=>$board_id,'post_date'=>$post_date,'is_cron_run'=>0);
            DB::table('cron_script')->insert($data);
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

        $social_account = $this->socialAccount->where('user_id', Sentinel::getUser()->id)->orderBy('created_at', 'DESC')->get()->first();

        // $token = session()->get('fb_access_token');
        $token = $social_account->facebook_token;
        // $this->api->setAccessToken($token);
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

            /**/
            if($get_method == 'POST') {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); 
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HEADER, 'Content-Type: application/x-www-form-urlencoded\r\n');
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($the_param));
                $result = curl_exec($ch);
            }
            else {
                $context  = stream_context_create($options);
                $result = file_get_contents($url, false, $context);
            }
            /**/
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

    /* Instagram */
    function image_load($filename) {   
        $image_info = getimagesize($filename); 
        $this->image_type = $image_info[2]; 
        if( $this->image_type == IMAGETYPE_JPEG ) {
            $this->image = imagecreatefromjpeg($filename);
        } elseif( $this->image_type == IMAGETYPE_GIF ) {  
            $this->image = imagecreatefromgif($filename); 
        } elseif( $this->image_type == IMAGETYPE_PNG ) {  
            $this->image = imagecreatefrompng($filename); 
        } 
        unset($image_info); 
    }

    function image_save($filename, $image_type=IMAGETYPE_JPEG, $compression=75, $permissions=null) {
        if( $image_type == IMAGETYPE_JPEG ) {
            imagejpeg($this->image,$filename,$compression);
        } elseif ( $image_type == IMAGETYPE_GIF ) {   
            imagegif($this->image,$filename); 
        } elseif ( $image_type == IMAGETYPE_PNG ) {   
            imagepng($this->image,$filename); 
        } 
        if( $permissions != null) {   
            chmod($filename,$permissions); 
        } 
    }

    function image_output($image_type=IMAGETYPE_JPEG) {   
        if( $image_type == IMAGETYPE_JPEG ) { 
            imagejpeg($this->image); 
        } elseif ( $image_type == IMAGETYPE_GIF ) {   
            imagegif($this->image); 
        } elseif ( $image_type == IMAGETYPE_PNG ) {   
            imagepng($this->image); 
        } 
    } 

    function image_getWidth() {   
        return imagesx($this->image); 
    } 

    function image_getHeight() {   
        return imagesy($this->image); 
    } 

    function image_resizeToHeight($height) {   
        $ratio = $height / $this->image_getHeight(); 
        $width = $this->image_getWidth() * $ratio; 
        $this->image_resize($width,$height); 
    }   

    function image_resizeToWidth($width) { 
        $ratio = $width / $this->image_getWidth(); 
        $height = $this->image_getheight() * $ratio; $this->image_resize($width,$height); 
    }   

    function image_scale($scale) { 
        $width = $this->image_getWidth() * $scale/100; 
        $height = $this->image_getheight() * $scale/100; 
        $this->image_resize($width,$height); 
    }   

    function image_resize($width,$height) { 
        $new_image = imagecreatetruecolor($width, $height); 
        imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->image_getWidth(), $this->image_getHeight()); $this->image = $new_image; 
    }

    public function insta_login($username, $password) {
        $this->username = $username;
        $this->password = $password;    
        $this->guid = $this->GenerateGuid();
        $device_id = "android-" . $this->guid;  
        $data = '{"device_id":"'.$device_id.'","guid":"'.$this->guid.'","username":"'. $this->username.'","password":"'.$this->password.'","Content-Type":"application/x-www-form-urlencoded; charset=UTF-8"}';
        $sig = $this->GenerateSignature($data);
        $data = 'signed_body='.$sig.'.'.urlencode($data).'&ig_sig_key_version=6';   
        return $this->insta_request('accounts/login/', true, $data, false);   
    }

    private function GenerateGuid() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', 
                mt_rand(0, 65535), 
                mt_rand(0, 65535), 
                mt_rand(0, 65535), 
                mt_rand(16384, 20479), 
                mt_rand(32768, 49151), 
                mt_rand(0, 65535), 
                mt_rand(0, 65535), 
                mt_rand(0, 65535));
    }

    private function GenerateSignature($data) {
        return hash_hmac('sha256', $data, $this->instaSignature); 
    }

    public function insta_post($photo, $caption, $schedule){
        $response = $this->insta_postImage($photo,$schedule);
        if(empty($response[1])) {
            echo "Empty response received from the server while trying to post the image";
            exit(); 
        }

        $obj = @json_decode($response[1], true);
        $status = $obj['status'];
        if($status == 'ok') {
            // Remove and line breaks from the caption
            $media_id = $obj['media_id'];       
            $response = $this->insta_postCaption($caption, $media_id);    
            return $response;
        }       
    }

    private function insta_postImage($photo,$schedule) {
        $data = $this->insta_getPostData($photo,$schedule);
        return $this->insta_request('media/upload/', true, $data, true);  
    }

    private function insta_postCaption($caption, $media_id) {
        $caption = preg_replace("/\r|\n/", "", $caption);
        $device_id = "android-".$this->guid;
        $data = '{"device_id":"'.$device_id.'","guid":"'. $this->guid .'","media_id":"'.$media_id.'","caption":"'.trim($caption).'","device_timestamp":"'.time().'","source_type":"5","filter_type":"0","extra":"{}","Content-Type":"application/x-www-form-urlencoded; charset=UTF-8"}';   
        $sig = $this->GenerateSignature($data);
        $new_data = 'signed_body='.$sig.'.'.urlencode($data).'&ig_sig_key_version=6';
        return $this->insta_request('media/configure/', true, $new_data, true);       
    }

    private function insta_getPostData($path,$schedule)  {
        $post_data = array('device_timestamp' => time());
        // $post_data = array('device_timestamp' => $schedule);
        if ((version_compare(PHP_VERSION, '5.5') >= 0)) {
            $post_data['photo'] = new \CURLFile(realpath($path));
        } else {
            $post_data['photo'] = "@".realpath($path);
        }
        return $post_data;
    }

    private function insta_request($url, $post, $post_data, $cookies) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->instagramUrl . $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        if($post) {
            curl_setopt($ch, CURLOPT_POST, 1);
            if ((version_compare(PHP_VERSION, '5.5') >= 0)) {
                curl_setopt($ch, CURLOPT_SAFE_UPLOAD, 1);
            }       
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        }
        
        if($cookies) {
            curl_setopt($ch, CURLOPT_COOKIEFILE,   dirname(__FILE__). '/cookies.txt');            
        } else {
            curl_setopt($ch, CURLOPT_COOKIEJAR,  dirname(__FILE__). '/cookies.txt');
        }
        $response = curl_exec($ch);
        $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);    
        curl_close($ch);    
        return array($http, $response);
    }
    /* Instagram */


    /* Cron */
    public function run_cron()
    {

        $callback_url = getenv('TWITTER_REDIRECT');
        $consumer_key = getenv('TWITTER_CLIENT_ID');
        $consumer_secret = getenv('TWITTER_CLIENT_SECRET');

        date_default_timezone_set('Asia/Kolkata');
        $current_time = date('Y-m-d H:i');
        $cronData = DB::select("SELECT * FROM cron_script WHERE post_date >= '".$current_time."' AND is_cron_run = 0");
        
        foreach ($cronData as $data) {
            $post_id = $data->post_id;
            $post_date = $data->post_date;

            if(strtotime($post_date) == strtotime($current_time)){
                if($data->type_name == 'twitter') {
                    
                    $postData = $this->post->find($post_id);
                    $title = $postData->title;

                    $oauth_token = $data->session;
                    $oauth_token_secret = $data->session_secret;

                    $url = 'https://api.twitter.com/1.1/statuses/update.json';
                    $parameters = array('status' => $title.' on '.date('d m Y H:i A'));
                    // $parameters = array('status' => $title);
                    $result = $this->Request($url, 'post', $consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret, $parameters);
                    if(isset($result['errors'])) {
                        echo "<pre>";
                        print_r($result);
                        echo "</pre>";
                    }
                    
                }
                else if($data->type_name == 'instagram') {

                    $post = $this->post->find($post_id);
                    $filename = $post->featured_image;
                    $title = $post->title;

                    $username = $data->session;
                    $password = $data->session_secret;
                    $schedule = $data->post_date;

                    $root = $_SERVER['DOCUMENT_ROOT'];
                    if($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
                        $new_filename = $root.$filename;
                    }
                    else {
                        $request_uri = '/public/';
                        $new_filename = $root.$request_uri.$filename;
                    }

                    $this->image_load($new_filename);
                    $this->image_resize(480,600);
                    $this->image_save($new_filename, IMAGETYPE_JPEG);

                    $response = $this->insta_login($username, $password);

                    if(strpos($response[1], "Sorry")) {
                        echo "Request failed, there's a chance that this proxy/ip is blocked";
                        print_r($response);
                        exit();
                    }         
                    if(empty($response[1])) {
                        echo "Empty response received from the server while trying to login";
                        print_r($response); 
                        exit(); 
                    }

                    $insta_post = $this->insta_post($new_filename, $title, $schedule);
                }
                else if ($data->type_name == 'pinterest') {
                    
                    $post = $this->post->find($post_id);
                    $filename = $post->featured_image;
                    $title = $post->title;

                    $filename = $post->featured_image;
                    $root = $_SERVER['DOCUMENT_ROOT'];
                    if($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
                        $image = $root.$filename;
                    }
                    else {
                        $request_uri = '/public/';
                        $image = $root.$request_uri.$filename;
                    }

                    $app_id = getenv('PINTEREST_CLIENT_ID');
                    $app_secret = getenv('PINTEREST_CLIENT_SECRET');
                    $callback_url = getenv('PINTEREST_REDIRECT');

                    $token = $data->session;
                    $board_id = $data->session_secret;

                    $pinterest = new Pinterest($app_id, $app_secret);
                    $pinterest->auth->setOAuthToken($token);
                    $caption = $title;

                    $pin = $pinterest->pins->create(array(
                        "note"          => $caption,
                        "image"     => $image,
                        "board"         => $board_id
                    ));
                }
            }

            
            DB::update('UPDATE cron_script SET is_cron_run = 1 WHERE id = ?' ,[$data->id]);
        }
    }

}
