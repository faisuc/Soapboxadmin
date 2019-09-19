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
    public function index($cell_id = null)
    {
    	$this->_loadSharedViews();

        $data = [];
        
        if ($cell_id == null) {

        }
        else {

        }

        // $user_id = $user_id ? $user_id : Sentinel::getUser()->id;
        // echo $cell_id;exit;

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
            'email_owner' => 'required|email|unique:social_cell,email_owner',
            'email_marketer' => 'required|email|unique:social_cell,email_marketer',
            'email_client' => 'required|email|unique:social_cell,email_client'
        ]);

        $cellname = $request->input('cellname');
        $email_owner = $request->input('email_owner');
        $email_marketer = $request->input('email_marketer');
        $email_client = $request->input('email_client');

        $socialcell = new $this->socialCell;
        $socialcell->cell_name = $cellname;
        $socialcell->email_owner = $email_owner;
        $socialcell->email_marketer	= $email_marketer;
        $socialcell->email_client = $email_client;
        $socialcell->payment_status = '1';
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
            'email_owner' => 'required|email|unique:social_cell,email_owner,$cell_id',
            'email_marketer' => 'required|email|unique:social_cell,email_marketer,$cell_id',
            'email_client' => 'required|email|unique:social_cell,email_client,$cell_id'
        ]);

        $cellname = $request->input('cellname');
        $email_owner = $request->input('email_owner');
        $email_marketer = $request->input('email_marketer');
        $email_client = $request->input('email_client');

        $socialcell = $this->socialCell->find($cell_id);
        $socialcell->cell_name = $cellname;
        $socialcell->email_owner = $email_owner;
        $socialcell->email_marketer = $email_marketer;
        $socialcell->email_client = $email_client;
        $socialcell->payment_status = '1';
        $socialcell->save();

        return redirect('socialcell')->with('flash_message', 'Social Cell has been Updated.');

    }

    public function delete($cell_id = null)
    {
        $this->socialCell->find($cell_id)->delete();
        return redirect()->back()->with('flash_message', 'Social Cell has been deleted.');

    }


}
