<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Sentinel;

class PostController extends Controller
{

    public function index($user_id = null)
    {

        $this->_loadSharedViews();

        $data = [];
        $data['posts'] = [];

        if ($user_id == null)
        {
            $data['posts'] = $this->post->where('user_id', Sentinel::getUser()->id)->orderBy('created_at', 'DESC')->get();
        }
        else
        {
            $data['posts'] = $this->post->where('user_id', $user_id)->get();
        }

        if (is_admin())
        {
            $data['managedClients'] = Sentinel::getUserRepository()->with('roles')->where('id', '<>', Sentinel::getUser()->id)->get();
        }
        else
        {
            $data['managedClients'] = $this->user->find(Sentinel::getUser()->id)->clients();
        }

        return view('pages.queues', $data);

    }

    public function create()
    {

        $this->_loadSharedViews();

        return view('pages.post-create');

    }

    public function store(Request $request)
    {

        $title = $request->input('title');
        $description = $request->input('description');
        $link = $request->input('link');
        $schedule_date = $request->input('schedule_date');

        $data = [];
        $media_id = 0;

        if ($request->has('photo'))
        {

            $photo = $request->file('photo');
            $fileName = uniqid() . $photo->getClientOriginalName();
            $filePath = '/public/medias/images/' . $fileName;
            $photo->storeAs('/public/medias/images/', $fileName);
            $data['photo'] = $fileName;

            $media = new $this->media;
            $media->post_id = 0;
            $media->type_id = 1;
            $media->file_name = $fileName;
            $media->file_ext = pathinfo($filePath, PATHINFO_EXTENSION);
            $media->save();

            $media_id = $media->id;

        }

        $post = new $this->post;
        $post->user_id = Sentinel::getUser()->id;
        $post->title = $title;
        $post->description = $description;

        if ($media_id != 0)
        {
            $post->featured_image_id = $media_id;
        }

        $post->link = $link;
        $post->schedule_to_post_date = Carbon::createFromFormat('Y-m-d H:i A', $schedule_date)->toDateTimeString();
        $post->save();

        if ($media_id != 0)
        {
            $media = $this->media->find($media_id);
            $media->post_id = $post->id;
            $media->save();
        }

        return redirect()->back()->with('flash_message', 'New post has been created.');

    }

    public function edit($post_id = null)
    {

        $this->_loadSharedViews();

        if ($this->post->find($post_id))
        {

            $data = [];

            if (is_client())
            {
                $can_edit = $this->post->where('user_id', Sentinel::getUser()->id)->where('id', $post_id)->first();

                if ( ! $can_edit)
                {
                    return redirect('dashboard');
                }

            }

            $data['post'] = $this->post->find($post_id);

            return view('pages.post-edit', $data);
        }

        return redirect('dashboard');

    }

    public function update(Request $request, $post_id = null)
    {

        $title = $request->input('title');
        $description = $request->input('description');
        $link = $request->input('link');
        $schedule_date = $request->input('schedule_date');
        $status = $request->input('status');

        $data = [];
        $media_id = 0;

        if ($request->has('photo'))
        {

            $photo = $request->file('photo');
            $fileName = uniqid() . $photo->getClientOriginalName();
            $filePath = '/public/medias/images/' . $fileName;
            $photo->storeAs('/public/medias/images/', $fileName);
            $data['photo'] = $fileName;

            $media = new $this->media;
            $media->post_id = 0;
            $media->type_id = 1;
            $media->file_name = $fileName;
            $media->file_ext = pathinfo($filePath, PATHINFO_EXTENSION);
            $media->save();

            $media_id = $media->id;

        }

        $post = $this->post->find($post_id);
        $post->title = $title;
        $post->description = $description;
        $post->status = $status;

        if ($media_id != 0)
        {
            $post->featured_image_id = $media_id;
        }

        $post->link = $link;
        $post->schedule_to_post_date = Carbon::createFromFormat('Y-m-d H:i A', $schedule_date)->toDateTimeString();
        $post->save();

        if ($media_id != 0)
        {
            $media = $this->media->find($media_id);
            $media->post_id = $post_id;
            $media->save();
        }

        return redirect()->back()->with('flash_message', 'Post has been updated.');

    }

    public function delete($post_id = null)
    {

        $this->post->find($post_id)->delete();

        return redirect()->back()->with('flash_message', 'Post has been deleted.');

    }

}
