<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Sentinel;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use App\Http\Controllers\TwitterAPIExchange;

class DashboardController extends Controller
{

    public function index()
    {

        $this->_loadSharedViews();

        $data = [];

        /* Facebook Page Info start */
        $facebook_account = $this->socialAccount->where('user_id', Sentinel::getUser()->id)->where('type_id', 1)->where('deleted_at', NULL)->orderBy('created_at', 'DESC')->get()->first();
        if(!empty($facebook_account)) {
        	
            $this->setFacebookObject();

            
            $user_id = Sentinel::getUser()->id;
            

        	if($facebook_account->facebook_token) {
                $token = $facebook_account->facebook_token;
                /*echo '<pre>';
                print_r($facebook_account);exit;*/

                // $accounts = $fb->get('me/accounts',$token);
                $accounts = $this->api->get('me/accounts',$token);
                $userData2 = $accounts->getDecodedBody();
                /*echo '<pre>';
                print_r($userData2);exit;*/

                $page_id = $userData2['data'][0]['id'];
                $page_token = $userData2['data'][0]['access_token'];
                // echo 'new_'.$page_token;exit;

                // $fandata = $fb->get($page_id.'?fields=talking_about_count,fan_count,rating_count ,new_like_count',$page_token);
                $fandata = $this->api->get($page_id.'?fields=talking_about_count,fan_count,rating_count ,new_like_count, posts.summary(true),published_posts.limit(1).summary(total_count).since(1)',$page_token);
                $fandata = $fandata->getDecodedBody();

                // echo $fandata['published_posts']['summary']['total_count'];exit;
                /*echo '<pre>';
                print_r($fandata);exit;*/


                $data['talking_about_count'] = $fandata['talking_about_count'];
                $data['fan_count'] = $fandata['fan_count'];
                $data['rating_count'] = $fandata['rating_count'];
                // $data['new_like_count'] = $fandata['new_like_count'];
                $data['published_posts_count'] = $fandata['published_posts']['summary']['total_count'];
                $data['facebook_follower'] = true;
             
                // $userdata = $this->api->get('/me/subscribers', $token);
                if($_SERVER['REMOTE_ADDR'] == '103.90.44.199') {
	                /*echo "<pre>";
	                print_r($userdata);
	                die();*/
                }
            }

        }
        /* Facebook Page Info End */

        /* Twitter Info start */
        $twitter_account = $this->socialAccount->where('user_id', Sentinel::getUser()->id)->where('type_id', 3)->where('deleted_at', NULL)->orderBy('created_at', 'DESC')->get()->first();
        if(!empty($twitter_account)) {
        	
            // echo $twitter_account->twitter_session.'==>'.$twitter_account->twitter_secret;exit;

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

                /*$url = "https://api.twitter.com/1.1/statuses/home_timeline.json";
                // $url = "https://api.twitter.com/1.1/followers/list.json";
                $requestMethod = "GET";
                $getfield = '';*/
                $url = "https://api.twitter.com/1.1/statuses/user_timeline.json";
                $requestMethod = "GET";
                $getfield = '?tweet_mode=extended';

                $twitter = new TwitterAPIExchange($settings);
                $string = json_decode($twitter->setGetfield($getfield)->buildOauth($url, $requestMethod)->performRequest(),$assoc = TRUE);


                if(!empty($string)) {
                    foreach($string as $tweets) {
                        $user_id = $tweets['user']['id_str'];
                        $name = $tweets['user']['name'];
                        $screen_name = $tweets['user']['screen_name'];
                        $followers = $tweets['user']['followers_count'];
                        $friends = $tweets['user']['friends_count'];
                        $likes = $tweets['user']['favourites_count'];
                        $statuses = $tweets['user']['statuses_count'];
                    }

                    $data['name'] = $name;
                    $data['screen_name'] = $screen_name;
                    $data['followers'] = $followers;
                    $data['friends'] = $friends;
                    $data['likes'] = $likes;
                    $data['statuses'] = $statuses;
                    $data['twitter_follower'] = true;

                    /**************************************************/
                    /*echo "<pre>";
                    print_r($data);
                    die();*/
                }
        	}
        }
        /* Twitter Info End*/

        /* Instagram Info Start*/
        $instagram_account = $this->socialAccount->where('user_id', Sentinel::getUser()->id)->where('type_id', 5)->where('deleted_at', NULL)->orderBy('created_at', 'DESC')->get()->first();
        if(!empty($instagram_account)) {
            /*$email = $instagram_account->instagram_user;
            $password = $instagram_account->instagram_password;*/
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
            /*echo "<pre>";
            print_r($response);
            echo "</pre>";exit;*/
            if(isset($response['graphql'])) {
                $data['total_fans'] = $response['graphql']['user']['edge_followed_by']['count'];
                $data['total_following'] = $response['graphql']['user']['edge_follow']['count'];
                $data['total_likes'] = $response['graphql']['user']['edge_saved_media']['count'];
                $data['total_posts'] = $response['graphql']['user']['edge_owner_to_timeline_media']['count'];
                $data['instagram_follower'] = true;
            }
        }
        /* Instagram Info End*/

        /* insert facebook,twitter,instagram info to table start */
        /*
        $current_date =  date('Y-m-d');
        $user_id = Sentinel::getUser()->id;

        $social = new $this->socialAccountInfo;
        $social->user_id = $user_id;
        $social->type_id = '0';
        $social->social_id = '0';
        $social->name = '0';
        
        $social->fb_talking_about_count = $data['talking_about_count'];
        $social->fb_fan_count = $data['fan_count'];
        $social->fb_rating_count = $data['rating_count'];
        $social->fb_published_posts_count = $data['published_posts_count'];
        
        $social->twt_followers_count = $data['followers'];
        $social->twt_following_count = $data['friends'];
        $social->twt_likes_count = $data['likes'];
        $social->twt_posts_count = $data['statuses'];

        $social->insta_followers_count = $data['total_fans'];
        $social->insta_following_count = $data['total_following'];
        $social->insta_likes_count = $data['total_likes'];
        $social->insta_posts_count = $data['total_posts'];
        
        $social->social_info_date = $current_date;

        $social->save(); */

        /* insert facebook info to table end */
        $user_id = Sentinel::getUser()->id;
        // echo date('Y-m-d').'<br>';exit;
        // echo date('Y-m-d', strtotime('-7 days'));exit;
        // $seven =  date('Y-m-d', strtotime('-7 days'));
        $seven =  date('Y-m-d');
        // echo $seven =  date('Y-m-d').'<br>';
        // echo $seven =  date('Y-m-d');exit;

        // $wh = ['field' => 'value', 'another_field' => 'another_value', ...];
        // $wh = ['user_id' => $user_id, 'created_at' => $seven];

        $data['past_info'] = $this->socialAccountInfo->where([['user_id','=',$user_id]])->orderBy('id', 'DESC')->limit(1)->get();
        // $data['past_info'] = $this->socialAccountInfo->where([['user_id','=',$user_id],['social_info_date','=',$seven ]])->orderBy('id', 'DESC')->limit(1)->get();
        
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

}
