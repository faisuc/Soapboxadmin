<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse
use Sentinel;
use Session;
use URL;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;

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
        
        $this->fb_connect_app();

        return redirect()->back()->with('flash_message', 'Social account has been added.');

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
        if(session()->get('fb_access_token') == '')
        {
            $helper = $this->api->getRedirectLoginHelper();
            $permissions = ['email','user_posts','manage_pages','publish_pages'];
            $loginUrl = $helper->getLoginUrl(URL::to('/').'/fb_callback', $permissions);
            // echo $loginUrl;
            return redirect($loginUrl);
            // redirect()->away($loginUrl);
            echo "Not Redirecting. Error Occur"; die();
        }

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

}
