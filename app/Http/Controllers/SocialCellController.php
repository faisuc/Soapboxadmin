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
    public function index($user_id = null)
    {
    	$this->_loadSharedViews();

        $data = [];
        
        if ($user_id == null) {
        }
        else {
        }

        $user_id = $user_id ? $user_id : Sentinel::getUser()->id;

        $data['socialcells'] = $this->socialCell->orderBy('created_at', 'DESC')->get();
        
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
    	/*echo '<pre>';
    	print_r($request->input());
    	exit;*/
        
        $cellname = $request->input('cellname');
        $cellemail1 = $request->input('cellemail1');
        $cellemail2 = $request->input('cellemail2');
        $cellemail3 = $request->input('cellemail3');

        $socialcell = new $this->socialCell;
        $socialcell->cell_name = $cellname;
        $socialcell->email_owner = $cellemail1;
        $socialcell->email_marketer	= $cellemail2;
        $socialcell->email_client = $cellemail3;
        $socialcell->payment_status = '1';
        $socialcell->save();

		return redirect('socialcell')->with('flash_message', 'Social Cell has been Created.');
		// return redirect('/socialaccounts')->with('flash_message', 'Social account has been added.');
     
    }
}
