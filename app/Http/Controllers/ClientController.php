<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Sentinel;

class ClientController extends Controller
{

    public function create($user_id = null)
    {

        $this->_loadSharedViews();

        if ($this->user->find($user_id) && canManageClients($user_id))
        {

            $data = [];
            $data['managedClients'] = $this->getManagedClients($user_id);

            return view('pages.user-clients', $data);

        }

        return redirect()->back();

    }

    public function delete($user_id = null, $client_id = null)
    {

        if (canManageClient($user_id, $client_id))
        {

            $this->client->where([
                ['user_id', '=', $user_id],
                ['client_id', '=', $client_id]
            ])->delete();

            return redirect()->back()->with('flash_message', 'Client has been removed to your list.');

        }

        return redirect()->back();

    }

}
