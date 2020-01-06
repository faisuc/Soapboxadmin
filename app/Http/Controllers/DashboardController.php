<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Sentinel;
use DateTime;
use DB;
use URL;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use App\Http\Controllers\TwitterAPIExchange;

class DashboardController extends Controller
{

    public function index($cell_id = null)
    {

        $this->_loadSharedViews();

        $data = [];

        if (is_admin())
        {
            $data['socialcells'] = $this->socialCell->orderBy('created_at', 'DESC')->get();
        }
        else
        {
            $loginUser = Sentinel::getUser();
            $loginUserEmail = $loginUser->email;
            
            $data['socialcells'] = $this->socialCell->where(function($query) use($loginUserEmail) {
                $query->where('user_id', Sentinel::getUser()->id)->orWhere('email_owner','like','%'.$loginUserEmail.'%')->orWhere('email_marketer','like','%'.$loginUserEmail.'%')->orWhere('email_client','like','%'.$loginUserEmail.'%');
            })->orderBy('created_at', 'DESC')->get();
        }

        if($cell_id == null) {
            $socialcell_id = (isset($data['socialcells'][0])) ? $data['socialcells'][0]->id : 0;
        }
        else {
            $socialcell_id = $cell_id;
        }
        $data['cell_id'] = $socialcell_id;

        if($socialcell_id) {
            
            /* Facebook Page Info start */
            $facebook_account = $this->socialAccount->where('type_id', 1)->where('social_cell_id',$socialcell_id)->orderBy('created_at', 'DESC')->get()->first();
            // $facebook_account = $this->socialAccount->where('type_id', 1)->where('social_cell_id',$socialcell_id)->orderBy('created_at', 'DESC')->get()->first();
            if(!empty($facebook_account)) {

                $this->setFacebookObject();
                
                $user_id = Sentinel::getUser()->id;

            	if($facebook_account->facebook_token) {
                    
                    $token = $facebook_account->facebook_token;
                    
                    $accounts = $this->api->get('me/accounts',$token);
                    $userData2 = $accounts->getDecodedBody();
                    
                    if(isset($userData2['data'][0])) {
                        $page_id = $userData2['data'][0]['id'];
                        $page_token = $userData2['data'][0]['access_token'];
                        // $fandata = $fb->get($page_id.'?fields=talking_about_count,fan_count,rating_count ,new_like_count',$page_token);
                        $fandata = $this->api->get($page_id.'?fields=talking_about_count,fan_count,rating_count ,new_like_count, posts.summary(true),published_posts.limit(1).summary(total_count).since(1)',$page_token);
                        $fandata = $fandata->getDecodedBody();

                        $fb_data['talking_about_count'] = (isset($fandata['talking_about_count'])) ? $fandata['talking_about_count'] : 0;
                        $fb_data['fan_count'] = (isset($fandata['fan_count'])) ? $fandata['fan_count'] : 0;
                        $fb_data['rating_count'] = (isset($fandata['rating_count'])) ? $fandata['rating_count'] : 0;
                        $fb_data['published_posts_count'] = (isset($fandata['published_posts']['summary']['total_count'])) ? $fandata['published_posts']['summary']['total_count'] : 0;
                        $data['facebook_follower'] = true;
                    }
                    else {
                        $fb_data['talking_about_count'] = 0;
                        $fb_data['fan_count'] = 0;
                        $fb_data['rating_count'] = 0;
                        $fb_data['published_posts_count'] = 0;
                    }
                    

                    $today = date('Y-m-d');
                    $check_fb_info = $this->socialAccountInfo->where('user_id', Sentinel::getUser()->id)->where('social_id',$facebook_account->id)->orderBy('id', 'DESC')->offset(1)->limit(1)->get()->first();
                    

                    if(!empty($check_fb_info)) {
                        $social_date = $check_fb_info->social_info_date;
                        $today = date('Y-m-d');
                        
                        $datetime1 = new DateTime($social_date);
                        $datetime2 = new DateTime($today);
                        $interval = $datetime1->diff($datetime2);
                        $days = $interval->format('%a');
                        
                        if($days < 7) {
                            $social_info_id = $check_fb_info->id;
                        }
                        else {
                            
                            // insert data into social account info                    
                            $social_fb = new $this->socialAccountInfo;
                            $social_fb->user_id = $user_id;
                            $social_fb->type_id = '1';
                            $social_fb->social_id = $facebook_account->id;
                            $social_fb->fb_talking_about_count = $fb_data['talking_about_count'];
                            $social_fb->fb_fan_count = $fb_data['fan_count'];
                            $social_fb->fb_rating_count = $fb_data['rating_count'];
                            $social_fb->fb_published_posts_count = $fb_data['published_posts_count'];
                            $social_fb->social_info_date = $today;
                            $social_fb->save();
                            // $social_info_id = $social_fb->id;
                            $social_info_id = $check_fb_info->id;
                        }

                    }
                    else {

                        $social_fb = new $this->socialAccountInfo;
                        $social_fb->user_id = $user_id;
                        $social_fb->type_id = '1';
                        $social_fb->social_id = $facebook_account->id;
                        $social_fb->fb_talking_about_count = $fb_data['talking_about_count'];
                        $social_fb->fb_fan_count = $fb_data['fan_count'];
                        $social_fb->fb_rating_count = $fb_data['rating_count'];
                        $social_fb->fb_published_posts_count = $fb_data['published_posts_count'];
                        $social_fb->social_info_date = $today;
                        $social_fb->save();

                        $social_info_id = $social_fb->id;

                    }
                    
                    $data['fb_data'] = $fb_data;
                    $data['fbpastinfo'] = $this->socialAccountInfo->where('id', $social_info_id)->get()->first();

                }
                

            }
            /* Facebook Page Info End */

            /* Twitter Info start */
            $twitter_account = $this->socialAccount->where('type_id', 3)->where('social_cell_id',$socialcell_id)->orderBy('created_at', 'DESC')->get()->first();
            // $twitter_account = $this->socialAccount->where('type_id', 3)->where('social_cell_id',$socialcell_id)->orderBy('created_at', 'DESC')->get()->first();
            if(!empty($twitter_account)) {
            	
                if($twitter_account->twitter_session && $twitter_account->twitter_secret) {

                    $consumer_key = getenv('TWITTER_CLIENT_ID');
                	$consumer_secret = getenv('TWITTER_CLIENT_SECRET');

    	        	$oauth_token = $twitter_account->twitter_session;
    	            $oauth_token_secret = $twitter_account->twitter_secret;

                    $settings = array(
                        'oauth_access_token' => $oauth_token,
                        'oauth_access_token_secret' => $oauth_token_secret,
                        'consumer_key' => $consumer_key,
                        'consumer_secret' => $consumer_secret
                    );

                    $url = "https://api.twitter.com/1.1/statuses/user_timeline.json";
                    $requestMethod = "GET";
                    $getfield = '?tweet_mode=extended';

                    $twitter = new TwitterAPIExchange($settings);
                    $user_timeline = json_decode($twitter->setGetfield($getfield)->buildOauth($url, $requestMethod)->performRequest(),$assoc = TRUE);
                    
                    if(!empty($user_timeline)) {
                        foreach($user_timeline as $tweets) {
                            $user_id = $tweets['user']['id_str'];
                            $name = $tweets['user']['name'];
                            $screen_name = $tweets['user']['screen_name'];
                            $followers = $tweets['user']['followers_count'];
                            $friends = $tweets['user']['friends_count'];
                            $likes = $tweets['user']['favourites_count'];
                            $statuses = $tweets['user']['statuses_count'];
                        }

                        $twt_data['name'] = $name;
                        $twt_data['screen_name'] = $screen_name;
                        $twt_data['followers'] = $followers;
                        $twt_data['friends'] = $friends;
                        $twt_data['likes'] = $likes;
                        $twt_data['statuses'] = $statuses;
                        $data['twitter_follower'] = true;
                        
                        $check_twt_info = $this->socialAccountInfo->where('user_id', Sentinel::getUser()->id)->where('social_id',$twitter_account->id)->orderBy('id', 'DESC')->limit(1)->get()->first();
                        
                        $today = date('Y-m-d');
                        $user_id = Sentinel::getUser()->id;

                        if(!empty($check_twt_info)) {
                            $social_date = $check_twt_info->social_info_date;
                            $today = date('Y-m-d');
                            
                            $datetime1 = new DateTime($social_date);
                            $datetime2 = new DateTime($today);
                            $interval = $datetime1->diff($datetime2);
                            $days = $interval->format('%a');
                            
                            if($days < 7) {
                                $social_info_id = $check_twt_info->id;
                            }
                            else {
                                // insert data into social account info                    
                                $social_twt = new $this->socialAccountInfo;
                                $social_twt->user_id = $user_id;
                                $social_twt->type_id = '3';
                                $social_twt->social_id = $twitter_account->id;
                                $social_twt->twt_followers_count = $twt_data['followers'];
                                $social_twt->twt_following_count = $twt_data['friends'];
                                $social_twt->twt_likes_count = $twt_data['likes'];
                                $social_twt->twt_posts_count = $twt_data['statuses'];
                                $social_twt->social_info_date = $today;
                                $social_twt->save();
                                // $social_info_id = $social_twt->id;
                                $social_info_id = $check_twt_info->id;
                            }

                        }
                        else {

                            // insert data into social account info                    
                            $social_twt = new $this->socialAccountInfo;
                            $social_twt->user_id = $user_id;
                            $social_twt->type_id = '3';
                            $social_twt->social_id = $twitter_account->id;
                            $social_twt->twt_followers_count = $twt_data['followers'];
                            $social_twt->twt_following_count = $twt_data['friends'];
                            $social_twt->twt_likes_count = $twt_data['likes'];
                            $social_twt->twt_posts_count = $twt_data['statuses'];
                            $social_twt->social_info_date = $today;
                            $social_twt->save();
                            
                            $social_info_id = $social_twt->id;
                        }
                        
                        // get social info id data
                        $data['twt_data'] = $twt_data;
                        $data['twtpastinfo'] = $this->socialAccountInfo->where('id', $social_info_id)->get()->first();

                    }
                    
            	}

            }
            /* Twitter Info End*/

            /* Instagram Info Start*/
            $instagram_account = $this->socialAccount->where('type_id', 5)->where('social_cell_id',$socialcell_id)->orderBy('created_at', 'DESC')->get()->first();
            // $instagram_account = $this->socialAccount->where('type_id', 5)->where('deleted_at', NULL)->orderBy('created_at', 'DESC')->get()->first();
            if(!empty($instagram_account)) {
                
                $username = $instagram_account->instagram_username;
                $apiurl = 'https://www.instagram.com/'.$username.'/?__a=1';
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $apiurl);
                curl_setopt($curl, CURLOPT_HEADER, false);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
                $response = curl_exec($curl);
                curl_close($curl);
                $response = json_decode($response,true);
                if(isset($response['graphql'])) {
                    $insta_data['total_fans'] = $response['graphql']['user']['edge_followed_by']['count'];
                    $insta_data['total_following'] = $response['graphql']['user']['edge_follow']['count'];
                    $insta_data['total_likes'] = $response['graphql']['user']['edge_saved_media']['count'];
                    $insta_data['total_posts'] = $response['graphql']['user']['edge_owner_to_timeline_media']['count'];
                    $data['instagram_follower'] = true;
                    
                    $check_insta_info = $this->socialAccountInfo->where('user_id', Sentinel::getUser()->id)->where('social_id',$instagram_account->id)->orderBy('id', 'DESC')->limit(1)->get()->first();
                    
                    $today = date('Y-m-d');

                    if(!empty($check_insta_info)) {
                        $social_date = $check_insta_info->social_info_date;
                        $today = date('Y-m-d');
                        
                        $datetime1 = new DateTime($social_date);
                        $datetime2 = new DateTime($today);
                        $interval = $datetime1->diff($datetime2);
                        $days = $interval->format('%a');
                        
                        if($days < 7) {
                            $social_info_id = $check_insta_info->id;
                        }
                        else {
                            // insert insta_data into social account info                    
                            $social_insta = new $this->socialAccountInfo;
                            $social_insta->user_id = $user_id;
                            $social_insta->type_id = '5';
                            $social_insta->social_id = $instagram_account->id;
                
                            $social_insta->insta_followers_count = $insta_data['total_fans'];
                            $social_insta->insta_following_count = $insta_data['total_following'];
                            $social_insta->insta_likes_count = $insta_data['total_likes'];
                            $social_insta->insta_posts_count = $insta_data['total_posts'];
                            
                            $social_insta->social_info_date = $today;
                            
                            $social_insta->save();
                            // $social_info_id = $social_insta->id;
                            $social_info_id = $check_insta_info->id;
                        }

                    }
                    else {
                        
                        $social_insta = new $this->socialAccountInfo;
                        $social_insta->user_id = $user_id;
                        $social_insta->type_id = '5';
                        $social_insta->social_id = $instagram_account->id;
            
                        $social_insta->insta_followers_count = $insta_data['total_fans'];
                        $social_insta->insta_following_count = $insta_data['total_following'];
                        $social_insta->insta_likes_count = $insta_data['total_likes'];
                        $social_insta->insta_posts_count = $insta_data['total_posts'];
                        
                        $social_insta->social_info_date = $today;
                        
                        $social_insta->save();
                        $social_info_id = $social_insta->id;
                    }
                    
                    $data['insta_data'] = $insta_data;
                    $data['instapastinfo'] = $this->socialAccountInfo->where('id', $social_info_id)->get()->first();
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
            /*$get_url = $url."?".http_build_query($the_param);
            $result = file_get_contents($get_url);        
            $json = json_decode($result, true);*/

            $ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
			$headers = array();
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			$json = curl_exec($ch);
			if (curl_errno($ch)) {
			    echo 'Error:' . curl_error($ch);
			}
			curl_close($ch);
            return $json;

            /*$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
			$headers = array();
			$headers[] = 'Content-Type: application/x-www-form-urlencoded\r\n';
			$headers[] = 'Authorization: OAuth oauth_consumer_key="'.$key.'", oauth_nonce="'.time().'", oauth_signature="'.$oauth_signature.'", oauth_signature_method="HMAC-SHA1", oauth_timestamp="'.time().'", oauth_token="'.$token.'", oauth_version="1.0"';
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			echo "<pre>";
			print_r($headers);
			die();
			$json = curl_exec($ch);
			if (curl_errno($ch)) {
			    echo 'Error:' . curl_error($ch);
			}
			curl_close($ch);
            return $json;*/
        }
    }
    /* Twitter */

    public function test_instagram() {

        if(isset($_SESSION['insta_fb'])) {

        }
        else {
            
            $this->setFacebookObject();

            $helper = $this->api->getRedirectLoginHelper();

            $permissions = ['instagram_basic','instagram_content_publish','instagram_manage_comments','instagram_manage_insights','manage_pages'];
            $loginUrl = $helper->getLoginUrl(URL::to('/').'/callback_instagram', $permissions);

            echo '<a href="' . htmlspecialchars($loginUrl) . '">Log in with Facebook!</a>';
        }
    }

}
