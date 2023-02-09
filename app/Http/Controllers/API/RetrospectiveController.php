<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller; 

use Auth; 
use Hash;
use Mail;
use Str;

use Illuminate\Http\Request; 

use App\Models\User;
use App\Models\Retrospective;


class RetrospectiveController extends Controller
{

    public function index()
    {
        $keyword = request()->keyword;

        $rows = Retrospective::where('id','>',0)->with('Milestone')->with('Sprint')->with('Project')->with('Status');

        if (!empty($keyword)) {
            $rows = $rows->where('title','LIKE','%'.$keyword.'%');
        }

        $rows = $rows->get();

        return response(['success' => 1, 'rows' => $rows]);
    }


    public function store()
    {
        $title = request()->title;
        $description = request()->description;
        $milestone_id = request()->milestone_id;
        $sprint_id = request()->sprint_id;
        $project_id = request()->project_id;
        $status_id = request()->status_id;
        $retro_type = request()->retro_type;

        if (empty($title)) {
            return response(['success' => 0, 'message' => 'Please enter a title...'],500);
        }

        $new = new Retrospective;
        $new->title = $title;
        $new->description = $description;
        $new->milestone_id = $milestone_id;
        $new->sprint_id = $sprint_id;
        $new->project_id = $project_id;
        $new->status_id = $status_id;
        $new->retro_type = $retro_type;

        $new->save();

        return response(['success' => 1, 'message' => 'New retrospective has been added!']);
    }

    public function update($id)
    {
        $title = request()->title;
        $description = request()->description;
        $milestone_id = request()->milestone_id;
        $sprint_id = request()->sprint_id;
        $project_id = request()->project_id;
        $status_id = request()->status_id;
        $retro_type = request()->retro_type;

        // var_dump($retro_type);
        // die();


        if (empty($title)) {
            return response(['success' => 0, 'message' => 'Please enter a title...'],500);
        }
        
        $data = Retrospective::find($id);
        $data->title = $title;
        $data->description = $description;
        $data->milestone_id = $milestone_id;
        $data->sprint_id = $sprint_id;
        $data->project_id = $project_id;
        $data->status_id = $status_id;
        $data->retro_type = $retro_type;

        $data->save();

 
        return response(['success' => 1, 'message' => $data->title.'  has been updated!']);
    }
   
}
