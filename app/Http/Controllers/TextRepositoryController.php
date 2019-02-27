<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Sentinel;

class TextRepositoryController extends Controller
{

    public function store(Request $request)
    {

        $text = new $this->textRepo;
        $text->user_id = Sentinel::getUser()->id;
        $text->title = $request->input('title');
        $text->content = $request->input('content');
        $text->save();

        return redirect()->back()->with('flash_message', 'New post has been added.');

    }

    public function delete(Request $request, $image_id = null)
    {

        if ($image_id)
        {

            $this->textRepo->destroy($image_id);

            return redirect()->back()->with('flash_message', 'Post has been deleted.');

        }

        return redirect()->back();

    }

}
