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

use Validator;
use Stripe;
use Illuminate\Mail\Mailable;
use Mail;
// use App\Http\Controllers\Stripe;
// use Stripe\Stripe;
// use Stripe\Charge;

class SocialCellController extends Controller
{
    private $api;
    public function index($status_id = null)
    {
        $this->_loadSharedViews();

        $status_id = ($status_id == 'all') ? null : $status_id;

        $data = [];
        
        if (is_admin())
        {
            if($status_id == null) {
                // $data['socialcells'] = $this->socialCell->orderBy('created_at', 'DESC')->get();
                $data['socialcells'] = $this->socialCell->whereIn('payment_status',array(1,2))->orderBy('created_at', 'DESC')->get();
            }
            else {
                $data['socialcells'] = $this->socialCell->where('payment_status',$status_id)->orderBy('created_at', 'DESC')->get();
            }
            $loginUser = Sentinel::getUser();
            $loginUserEmail = $loginUser->email;
            $data['user_email'] = $loginUserEmail;
        }
        else
        {
            $loginUser = Sentinel::getUser();
            $loginUserEmail = $loginUser->email;
            $data['user_email'] = $loginUserEmail;

            if($status_id == null) {
                // $data['socialcells'] = $this->socialCell->where('user_id', Sentinel::getUser()->id)->orWhere('email_owner','like','%'.$loginUserEmail.'%')->orWhere('email_marketer','like','%'.$loginUserEmail.'%')->orWhere('email_client','like','%'.$loginUserEmail.'%')->orderBy('created_at', 'DESC')->get();
                $data['socialcells'] = $this->socialCell->where(function($query) use($loginUserEmail) {
                    $query->where('user_id', Sentinel::getUser()->id)->orWhere('email_owner','like','%'.$loginUserEmail.'%')->orWhere('email_marketer','like','%'.$loginUserEmail.'%')->orWhere('email_client','like','%'.$loginUserEmail.'%');
                })->whereIn('payment_status',array(1,2))->orderBy('created_at', 'DESC')->get();
            }
            else {
                $data['socialcells'] = $this->socialCell->where(function($query) use($loginUserEmail) {
                    $query->where('user_id', Sentinel::getUser()->id)->orWhere('email_owner','like','%'.$loginUserEmail.'%')->orWhere('email_marketer','like','%'.$loginUserEmail.'%')->orWhere('email_client','like','%'.$loginUserEmail.'%');
                })->where('payment_status',$status_id)->orderBy('created_at', 'DESC')->get();
            }
        }
        // $data['socialcells'] = $this->socialCell->orderBy('created_at', 'DESC')->get();
        $data['statuses'] = $this->payment_statuses();
        $data['status_id'] = $status_id;
        
        return view('pages.social-cells', $data);
        // return view('pages.social-cells', $data);
    }

    public function date_filter($start_date,$end_date)
    {
        $this->_loadSharedViews();

        $data = [];
        
        if (is_admin())
        {
            $loginUser = Sentinel::getUser();
            $loginUserEmail = $loginUser->email;
            $data['user_email'] = $loginUserEmail;
            $data['socialcells'] = $this->socialCell->whereBetween('created_at',[$start_date,$end_date])->orderBy('created_at', 'DESC')->get();
        }
        else
        {
            $loginUser = Sentinel::getUser();
            $loginUserEmail = $loginUser->email;
            $data['user_email'] = $loginUserEmail;

            $data['socialcells'] = $this->socialCell->whereBetween('created_at',[$start_date,$end_date])->where('user_id', Sentinel::getUser()->id)->orWhere('email_owner','like','%'.$loginUserEmail.'%')->orWhere('email_marketer','like','%'.$loginUserEmail.'%')->orWhere('email_client','like','%'.$loginUserEmail.'%')->orderBy('created_at', 'DESC')->get();
            
        }
        
        // $data['socialcells'] = $this->socialCell->orderBy('created_at', 'DESC')->get();
        $data['statuses'] = $this->payment_statuses();
        $data['status_id'] = 'all';
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        
        return view('pages.social-cells', $data);
    }

    public function add_social_cell($id='')
    {
    	$this->_loadSharedViews();
    	
    	$data = [];
		
		return view('pages.cell-create', $data);
    }


    public function store(Request $request)
    {
    	$validatedData = $request->validate([
            'cellname' => 'required|min:4',
        ]);

        /**/
        $loginUser = Sentinel::getUser();
        $loginUserEmail = $loginUser->email;

        $cellname = $request->input('cellname');
        $email_owner = $request->input('email_owner');
        $email_marketer = $request->input('email_marketer');
        $email_client = $request->input('email_client');
        $payment_status = '1';//$request->input('payment_status');

        $ownerEmail = explode(',', $email_owner);
        $marketerEmail = explode(',', $email_marketer);
        $clientEmail = explode(',', $email_client);

        if(in_array($loginUserEmail, $ownerEmail) || in_array($loginUserEmail, $marketerEmail) || in_array($loginUserEmail, $clientEmail)) {
            
            $checkCellName = $this->socialCell->where('cell_name',$cellname)->where('user_id', Sentinel::getUser()->id)->get();
            
            if(count($checkCellName) > 0) {
                
                return redirect()->back()->withErrors(['Cell Name : '.$cellname.' Already Exists!']);
            }
            else {

                $socialcell = new $this->socialCell;
                $socialcell->user_id = Sentinel::getUser()->id;
                $socialcell->cell_name = $cellname;
                $socialcell->email_owner = $email_owner;
                $socialcell->email_marketer	= $email_marketer;
                $socialcell->email_client = $email_client;
                $socialcell->payment_status = $payment_status;

                if($request->input('post_status') != '') {
                    $socialcell->post_status = '1';
                }

                $socialcell->save();

                $cell_id = $socialcell->id;

        		if($request->input('payment') != '') {
                    return redirect('generate_payment/'.$cell_id);
                }
                else {

                    $html = 'Please Make Payment for Your Cell <strong>'.$cellname.'</strong>'.'<br>'.'<br>'.
                    '<a class="btn btn-primary" href="'.URL::to('/').'/generate_payment/'.$socialcell->id.'">Make Payment</a>';
                    foreach ($ownerEmail as $owner_email) {
                        $userdata = new $this->user;
                        $userdata->email = $owner_email;
                        $userdata->html = $html;
                        Mail::send([], [], function ($message) use ($userdata) { 
                            $html = $userdata->html;
                            $message->to($userdata->email)->subject('Social Cell make Payment')->setBody($html, 'text/html'); 
                        });
                    }

                    return redirect('socialcell/edit/'.$cell_id)->with('flash_message', 'Social Cell has been Updated.');
                }
                // return redirect('socialcell')->with('flash_message', 'Social Cell has been Created.');
            }
        }
        else {
            
            return redirect('/socialcell/add')->withInput()->withErrors(['Please fill your email in any of the below.']);
        }
        
     
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
        ]);

        $loginUser = Sentinel::getUser();
        $loginUserEmail = $loginUser->email;

        $cellname = $request->input('cellname');
        $email_owner = $request->input('email_owner');
        $email_marketer = $request->input('email_marketer');
        $email_client = $request->input('email_client');
        $payment_status = '1';//$request->input('payment_status');

        $ownerEmail = explode(',', $email_owner);
        $marketerEmail = explode(',', $email_marketer);
        $clientEmail = explode(',', $email_client);

        if(in_array($loginUserEmail, $ownerEmail) || in_array($loginUserEmail, $marketerEmail) || in_array($loginUserEmail, $clientEmail)) {

            $socialcell = $this->socialCell->find($cell_id);
            $socialcell->cell_name = $cellname;
            $socialcell->email_owner = $email_owner;
            $socialcell->email_marketer = $email_marketer;
            $socialcell->email_client = $email_client;
            $socialcell->payment_status = $payment_status;

            if($request->input('post_status') != '') {
                $socialcell->post_status = '1';
            }
            
            $socialcell->save();

            $cell_id = $socialcell->id;

            if($request->input('payment') != '') {
                return redirect('generate_payment/'.$cell_id);
            }
            else {

                return redirect('socialcell/edit/'.$cell_id)->with('flash_message', 'Social Cell has been Updated.');
            }
            
        }
        else {
            
            return redirect('socialcell/edit/'.$cell_id)->withInput()->withErrors(['Please fill your email in any of the below.']);
        }

    }

    public function delete($cell_id = null)
    {
        $this->socialCell->find($cell_id)->delete();
        return redirect()->back()->with('flash_message', 'Social Cell has been deleted.');
    }

    public function cancel_payment($cell_id = null)
    {
        $socialcell = $this->socialCell->find($cell_id);
        $socialcell->payment_status = '3';
        $socialcell->save();
        return redirect()->back()->with('flash_message', 'Social Cell has been Cancelled.');
    }

    public function onhold_payment($cell_id = null)
    {
        $socialcell = $this->socialCell->find($cell_id);
        $socialcell->payment_status = '4';
        $socialcell->save();
        return redirect()->back()->with('flash_message', 'Social Cell has been On Hold.');
    }

    public function active_payment($cell_id = null)
    {
        $socialcell = $this->socialCell->find($cell_id);
        $socialcell->payment_status = '2';
        $socialcell->save();
        return redirect()->back()->with('flash_message', 'Social Cell has been Active.');
    }

    public function generate_payment($cell_id)
    {
        $this->_loadSharedViews();

        $data['cell_id'] = $cell_id;
        $cells = $this->socialCell->find($cell_id);
        $data['cell_name'] = $cells->cell_name;
        $customer = $this->user->find($cells->user_id);
        $data['customer_email'] = $customer->email;
        return view('pages.stripe-form', $data);
    }

    public function postPaymentStripe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'card_no' => 'required',
            'ccExpiryMonth' => 'required',
            'ccExpiryYear' => 'required',
            'cvvNumber' => 'required',
        ]);


        $input = $request->all();
        if ($validator->passes()) { 
            $input = array_except($input,array('_token'));
            
            // require_once(APP_PATH.'vendor/stripe/stripe-php/init.php');

            $stripe_sandbox = getenv('STRIPE_SANDBOX');
            $stripe_test_key = getenv('STRIPE_TEST_KEY');
            $stripe_test_secret = getenv('STRIPE_TEST_SECRET');
            $stripe_live_key = getenv('STRIPE_PUBLIC_KEY');
            $stripe_live_secret = getenv('STRIPE_PUBLIC_SECRET');
            $cell_id = $request->input('cell_id');
            $email = $request->input('email');
            $validity_date = date('Y-m-d',strtotime("+30 days"));
            
            $amount = $request->input('amount');
            $amount = (int)($amount * 100);
            
            if($stripe_sandbox) {

                \Stripe\Stripe::setApiKey($stripe_test_secret);
            }
            else {
                \Stripe\Stripe::setApiKey($stripe_live_secret);
            }

            try {

                $plan = \Stripe\Plan::create(array(
                    "product" => [
                        "name" => "Socialhat",
                        "type" => "service"
                    ],
                    "nickname" => "Socialhat",
                    "interval" => "month",
                    "interval_count" => "1",
                    "currency" => "usd",
                    "amount" => $amount,
                ));
                
                $token = \Stripe\Token::create([
                    'card' => [
                        'number' => $request->input('card_no'),
                        'exp_month' => $request->input('ccExpiryMonth'),
                        'exp_year' => $request->input('ccExpiryYear'),
                        'cvc' => $request->input('cvvNumber'),
                    ],
                ]);

                if (!isset($token['id'])) {
                    
                    return redirect('/generate_payment/'.$cell_id)->withErrors(['Token Not Generated.']);
                }

                /*$charge = \Stripe\Charge::create([
                    // 'card' => $token['id'],
                    'currency' => 'USD',
                    'amount' => $amount,
                    // 'description' => 'wallet',
                    'source' => $token
                ]);*/
                $customer = \Stripe\Customer::create([
                    'email' => $email,
                    'source'  => $token,
                ]);

                $subscription = \Stripe\Subscription::create(array(
                    "customer" => $customer->id,
                    "items" => array(
                        array(
                            "plan" => $plan->id,
                        ),
                    ),
                ));

                // if($charge['status'] == 'succeeded') {
                if($subscription['status'] == 'active') {
                    
                    $socialcell = $this->socialCell->find($cell_id);
                    $socialcell->payment_status = '2';
                    $socialcell->payment_validity = $validity_date;
                    $socialcell->save();
                    
                    return redirect('/socialcell/edit/'.$cell_id)->with('flash_message','Payment Generated Successfully..');
                    
                } else {
                    
                    return redirect('/generate_payment/'.$cell_id)->withErrors(['Money not add in wallet!!']);
                }
            }
            catch (Exception $e) {
                
                return redirect('/generate_payment/'.$cell_id)->withErrors([$e->getMessage()]);
            }
            catch(\Cartalyst\Stripe\Exception\CardErrorException $e) {
                
                return redirect('/generate_payment/'.$cell_id)->withErrors([$e->getMessage()]);
            }
            catch(\Cartalyst\Stripe\Exception\MissingParameterException $e) {

                return redirect('/generate_payment/'.$cell_id)->withErrors([$e->getMessage()]);
            }
        }
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
            $loginUrl = $helper->getLoginUrl(URL::to('/').'/fb_callback', $permissions);
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
