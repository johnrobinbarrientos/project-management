<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\Sprint;
use App\Models\Task;
use App\Models\Project;

class SprintController extends Controller
{
    public function index()
    {
        $keyword = request()->keyword;

        $rows = Sprint::where('id','>',0)->with('Project')->with('Sprint');
        
        if (!empty($keyword)) {
            $rows = $rows->where('title','LIKE','%'.$keyword.'%');
        }

        $rows = $rows->get();

        $x = 0;

        foreach ($rows as $row) {

            $total_task_duration = Task::where('sprint_id',$row->id)->sum('task_duration');

            $rows[$x]['total_task_duration'] = (int)$total_task_duration;
            $x++;
        }

        return response(['success' => 1, 'rows' => $rows]);
    }


    public function store()
    {
        $title = request()->title;
        $description = request()->description;
        $project_id = request()->project_id;
        $sprint_start_date = request()->sprint_start_date;
        $sprint_due_date = request()->sprint_due_date;
        $sprint_duration = request()->sprint_duration;
        $depends_id = request()->depends_id;
        
        if (empty($title)) {
            return response(['success' => 0, 'message' => 'Please enter a sprint title...'],500);
        }

        $new = new Sprint;
        $new->title = $title;
        $new->description = $description;
        $new->project_id = $project_id;
        $new->sprint_start_date = $sprint_start_date;
        $new->sprint_due_date = $sprint_due_date;
        $new->sprint_duration = $sprint_duration;
        $new->depends_id = $depends_id;
        $new->save();

        return response(['success' => 1, 'message' => 'New Sprint has been added!', 'data' => $new]);
    }

    public function deleteBulk()
    {
        $ids = request()->ids;
        $count = 0;
        foreach ($ids as $id) {
            $sprint = Sprint::find($id);

            if (!$sprint)  {
                continue;
            }

            $count++;
            $sprint->delete();
        }

        return response(['success' => 1, 'message' => $count.' sprints  has been deleted!']);
    }

    public function duplicate()
    {
        $project_id = request()->project_id;
        $project_id = $project_id['id'];
        $sprint_ids = request()->sprint_ids;

        $project = Project::find($project_id);

        if (!$project) {
            return response(['success' => 0, 'message' => 'Could not find the project...'],500);
        }

        $new_sprint_ids = [];

        foreach ($sprint_ids as $id) {
            $find  = Sprint::find($id);

            $new = new Sprint;

            $new->board_id = null;
            $new->project_id = $project->id;

            $new->title = $find->title;
            $new->description = $find->description;
            $new->sprint_start_date = $find->sprint_start_date;
            $new->sprint_due_date = $find->sprint_due_date;
            $new->depends_id = null;
            $new->save();

            $new_sprint_ids[] = $new->id;

        }

        $rows = Sprint::whereIn('id',$new_sprint_ids)->get();
        return response(['success' => 1, 'message' => 'Sprints has been duplicated!', 'rows' => $rows]);
    }

    public function update($id)
    {
        $title = request()->title;
        $description = request()->description;
        $project_id = request()->project_id;
        $sprint_start_date = request()->sprint_start_date;
        $sprint_due_date = request()->sprint_due_date;
        $sprint_duration = request()->sprint_duration;
        $depends_id = request()->depends_id;

        if (empty($title)) {
            return response(['success' => 0, 'message' => 'Please enter a sprint title...'],500);
        }
        
        $data = Sprint::find($id);
        $data->title = $title;
        $data->description = $description;
        $data->project_id = $project_id;
        $data->sprint_start_date = $sprint_start_date;
        $data->sprint_due_date = $sprint_due_date;
        $data->sprint_duration = $sprint_duration;
        $data->depends_id = $depends_id;
        $data->save();
 
        return response(['success' => 1, 'message' => $data->title.'  has been updated!']);
    }

    public function tagComplete($id)
    {
        $data = Sprint::find($id);
        $data->sprint_completed_date = date("Y-m-d");
        $data->save();
 
        return response(['success' => 1, 'message' => 'updated!']);
    }

    public function tagUncomplete($id)
    {
        $data = Sprint::find($id);
        $data->sprint_completed_date = null;
        $data->save();
 
        return response(['success' => 1, 'message' => 'updated!']);
    }

    public function updateBulk()
    {
        $ids = request()->ids;
        $fields = request()->fields;

        $count = 0;
        foreach ($ids as $id) {
            
            $sprint_start_date = $fields['sprint_start_date'];
            $sprint_due_date = $fields['sprint_due_date'];
            $project_id = $fields['project_id'];


            $data = Sprint::find($id);

            if (!$data) {
                continue;
            }

            $count++;

            if (!empty($sprint_start_date) || !is_null($sprint_start_date)) {
                $data->sprint_start_date = $sprint_start_date;
            }

            if (!empty($sprint_due_date) || !is_null($sprint_due_date)) {
                $data->sprint_due_date = $sprint_due_date;
            }

            if (!empty($project_id) || !is_null($project_id)) {
                $data->project_id = $project_id;
            }

            $data->save();
        }

        return response(['success' => 1, 'message' => $count.'  sprints has been updated!']);
    }
}