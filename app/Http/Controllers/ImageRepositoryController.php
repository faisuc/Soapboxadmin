<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Sentinel;

class ImageRepositoryController extends Controller
{

    public function store(Request $request)
    {

        if ($request->has('image'))
        {

            $image = $request->file('image');
            $fileName = uniqid() . $image->getClientOriginalName();
            $filePath = '/public/medias/images/' . $fileName;
            $image->storeAs('/public/medias/images/', $fileName);
            $credentials['image'] = $fileName;

            $imageRepo = new $this->imageRepo;
            $imageRepo->user_id = Sentinel::getUser()->id;
            $imageRepo->type_id = 1;
            $imageRepo->file_name = $fileName;
            $imageRepo->file_ext = pathinfo($filePath, PATHINFO_EXTENSION);
            $imageRepo->save();

            return redirect()->back()->with('flash_message', 'New image has been uploaded.');

        }

        return redirect()->back();

    }

    public function delete(Request $request, $image_id = null)
    {

        if ( $image_id )
        {

            $this->imageRepo->destroy($image_id);

            return redirect()->back()->with('flash_message', 'Image has been deleted.');

        }

        return redirect()->back();

    }

}
