<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller; 

use Auth; 
use Hash;
use Mail;
use Str;

use Illuminate\Http\Request; 

use App\Models\User;
use App\Models\Tag;


class TagController extends Controller
{

    public function index()
    {
        $rows = Tag::with('project')->get();

        return response(['success' => 1, 'rows' => $rows]);
    }


    public function store()
    {
        $project_id = request()->project_id;
        $title = request()->title;
        $text_color = (request()->color) ? request()->color : '#333333';
        $background_color = (request()->background) ? request()->background : '#5b75aa';

        if (empty($project_id) || is_null($project_id)) {
            return response(['success' => 0, 'message' => 'Please select a project...'],500);
        }

        if (empty($title) || is_null($title)) {
            return response(['success' => 0, 'message' => 'Please enter title...'],500);
        }

        $new = new Tag;
        $new->project_id = $project_id;
        $new->title = $title;
        $new->color = $text_color;
        $new->background = $background_color;
        $new->save();

        return response(['success' => 1, 'message' => 'New tag has been added!', 'data' => $new]);
    }

    public function update($id)
    {
        $project_id = request()->project_id;
        $title = request()->title;
        $text_color = (request()->color) ? request()->color : '#333333';
        $background_color = (request()->background) ? request()->background : '#5b75aa';


        if (empty($project_id) || is_null($project_id)) {
            return response(['success' => 0, 'message' => 'Please select a project...'],500);
        }

        $data = Tag::find($id);
        $data->project_id = $project_id;
        $data->title = $title;
        $data->color = $text_color;
        $data->background = $background_color;
        $data->save();

        return response(['success' => 1, 'message' => $data->title.' tag has been updated!', 'data' => $data]);
    }
   
}
