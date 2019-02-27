<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Sentinel;
use DB;

class PostNotesController extends Controller
{

    public function store(Request $request)
    {

        if ($request->ajax())
        {

            try {

                $content = $request->input('content');
                $post_id = $request->input('post_id');

                $note = new $this->postNotes;
                $note->user_id = Sentinel::getUser()->id;
                $note->content = $content;
                $note->post_id = $post_id;
                $note->save();

                $note = $this->postNotes->select(
                    DB::raw('post_notes.*, users.*, post_notes.created_at as created_at, post_notes.id as id')
                )->join('users', function($join) {
                    $join->on('post_notes.user_id', '=', 'users.id');
                })->where('post_notes.id', '=', $note->id)
                ->first();

                return response()->json(['success' => true, 'collection' => $note]);

            } catch(\Exception $e) {

                return response()->json(['success' => false, 'message' => $e->getMessage()]);

            }

        }

    }

    public function collection(Request $request)
    {

        if ($request->ajax())
        {

            $post_id = $request->input('post_id');
            $notes = $this->postNotes->select(
                DB::raw('post_notes.*, users.*, post_notes.created_at as created_at, post_notes.id as id')
            )->join('users', function($join) {
                $join->on('post_notes.user_id', '=', 'users.id');
            })->where('post_notes.post_id', '=', $post_id)
            ->get();

            return response()->json(['collections' => $notes]);

        }

    }

    public function delete(Request $request)
    {

        if ($request->ajax())
        {

            $this->postNotes->destroy($request->input('note_id'));

            return response()->json(['success' => true]);

        }

    }

}
