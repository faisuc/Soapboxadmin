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
                // $userdata = $this->api->get('/me/subscribers', $token);
                if($_SERVER['REMOTE_ADDR'] == '103.90.44.199') {
	                /*echo "<pre>";
	                print_r($userdata);
	                die();*/
                }
            }

        }
        /* Facebook Page */
        /* Twitter */
        $twitter_account = $this->socialAccount->where('user_id', Sentinel::getUser()->id)->where('type_id', 3)->where('deleted_at', NULL)->orderBy('created_at', 'DESC')->get()->first();
        if(!empty($twitter_account)) {
        	
        	if($twitter_account->twitter_session && $twitter_account->twitter_secret) {

        		$consumer_key = getenv('TWITTER_CLIENT_ID');
            	$consumer_secret = getenv('TWITTER_CLIENT_SECRET');

	        	$oauth_token = $twitter_account->twitter_session;
	            $oauth_token_secret = $twitter_account->twitter_secret;

	            $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
	            $parameters = array('screen_name' => 'REPLACE_ME');
	            $result = $this->Request($url, 'post', $consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret, $parameters);

	            if($_SERVER['REMOTE_ADDR'] == '103.90.44.199') {
	                echo "<pre>";
	                print_r($result);
	                die();
	            }
        	}
        }


        return view('pages.dashboard', $data);

    }

    /* Facebook */
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
    /* Facebook */

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

}
