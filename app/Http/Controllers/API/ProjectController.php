<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller; 

use Auth; 
use Hash;
use Mail;
use Str;

use Illuminate\Http\Request; 

use App\Models\User;
use App\Models\Project;
use App\Models\ProjectUser;
use App\Models\Task; 
use App\Models\Module; 
use App\Models\Board; 


class ProjectController extends Controller
{

    public function index()
    {
        $keyword = request()->keyword;

        $rows = Project::where('id','>',0)->with('ProjectUsers');

        if (!empty($keyword)) {
            $rows = $rows->where('title','LIKE','%'.$keyword.'%');
        }


         /* pagination start */
        //$count = $rows->count();
        $take = (is_numeric(request()->take) && request()->take <= 300) ? request()->take : 20;
        $page = (is_numeric(request()->page)) ? request()->page : 1;
        $offset = (($page - 1 ) * $take);
         
        $rows = $rows->take($take);
        $rows = $rows->offset($offset);
         /* pagination end */

        $rows = $rows->get();

        return response(['success' => 1, 'rows' => $rows]);
    }

    public function getDetails($id)
    {

        $data = Project::find($id);

        return response(['success' => 1, 'data' => $data]);
    }

    public function getDuration($id)
    {

        $duration = Task::where('project_id',$id)->sum('task_duration');

        return response(['success' => 1, 'rows' => $duration]);
    
    }


    public function store()
    {
        $title = request()->title;
        $description = request()->description;

        $is_active = request()->is_active;
        $sprints_yes = request()->sprints_yes;
        $milestones_yes = request()->milestones_yes;
        $ideas_yes = request()->ideas_yes;
        $retrospectives_yes = request()->retrospectives_yes;
        $timesheet_yes = request()->timesheet_yes;
        $chat_yes = request()->chat_yes;
        $duration = request()->duration;
        $project_start_date = request()->project_start_date;
        $project_due_date = request()->project_due_date;
        $project_completed_date = request()->project_completed_date;

        $user_details = request()->user_details;

        if (empty($title)) {
            return response(['success' => 0, 'message' => 'Please enter a project title...'],500);
        }

        // if (strlen($description) < 5) {
        //     return response(['success' => 0, 'message' => 'Project descritpion is too short...'],500);
        // }

       
        $find_slug = true;
        $count = 0;
        while($find_slug) {
            $slug = substr($title,0,5);
            $slug = ($count > 0) ? Str::slug($slug.'-'.$count, '-') : Str::slug($slug, '-');
           
            $exists = Project::where('slug','=',$slug)->first();

            if (!$exists) {
                $find_slug = false;
            }

            $count++;
        }
        
        $new = new Project;
        $new->title = $title;
        $new->slug = $slug;
        $new->description = $description;
        $new->is_active = $is_active;
        $new->sprints_yes = $sprints_yes;
        $new->milestones_yes = $milestones_yes;
        $new->ideas_yes = $ideas_yes;
        $new->retrospectives_yes = $retrospectives_yes;
        $new->timesheet_yes = $timesheet_yes;
        $new->duration = $duration;
        $new->project_start_date = $project_start_date;
        $new->project_due_date = $project_due_date;
        $new->project_completed_date = $project_completed_date;
        $new->theme_id = 0;
        $new->save();

        $module = Module::where('name','=','Tasks')->first();

        $board = Board::where('module_id','=',$module->id)->where('project_id','=',$new->id)->where('is_default','=',true)->first();

        if (!$board) {
            $total_boards = Board::where('module_id','=',$module->id)->get()->count();
            $board = new Board();
            $board->title = 'Shelf';
            $board->module_id = $module->id;
            $board->project_id = $new->id;
            $board->is_default = true;
            $board->order = $total_boards + 1;
            $board->save();
        }

        //*********************** Add Project Users/
        foreach ($user_details as $user_detail) {

            $projectUsers = ProjectUser::where('project_id','=',$new->id)->where('user_id','=',$user_detail['id'])->first();
            $projectUsers = (!$projectUsers) ? new ProjectUser : $projectUsers;
            $projectUsers->project_id = $new->id;
            $projectUsers->user_id = $user_detail['id'];
            $projectUsers->save();
        }

        return response(['success' => 1, 'message' => 'New project has been added!']);
    }

    public function update($id)
    {
        $title = request()->title;
        $description = request()->description;

        $is_active = request()->is_active;
        $sprints_yes = request()->sprints_yes;
        $milestones_yes = request()->milestones_yes;
        $ideas_yes = request()->ideas_yes;
        $retrospectives_yes = request()->retrospectives_yes;
        $timesheet_yes = request()->timesheet_yes;
        $chat_yes = request()->chat_yes;
        $duration = request()->duration;
        $project_start_date = request()->project_start_date;
        $project_due_date = request()->project_due_date;
        $project_completed_date = request()->project_completed_date;

        $user_details = request()->user_details;

        if (empty($title)) {
            return response(['success' => 0, 'message' => 'Please enter a project title...'],500);
        }

        if (strlen($description) < 5) {
            return response(['success' => 0, 'message' => 'Project descritpion is too short...'],500);
        }

        
        $data = Project::find($id);
        $data->title = $title;
        $data->description = $description;

        $data->is_active = $is_active;
        $data->sprints_yes = $sprints_yes;
        $data->milestones_yes = $milestones_yes;
        $data->ideas_yes = $ideas_yes;
        $data->retrospectives_yes = $retrospectives_yes;
        $data->timesheet_yes = $timesheet_yes;
        $data->chat_yes = $chat_yes;
        $data->duration = $duration;
        $data->project_start_date = $project_start_date;
        $data->project_due_date = $project_due_date;
        $data->project_completed_date = $project_completed_date;
        $data->save();


         //*********************** Updating Project Users/
         $user_ids = [];
        
         foreach ($user_details as $key => $detail) {
             $user_ids[] = $detail['id'];
         }
 
         // delete details
         ProjectUser::where('project_id','=',$data->id)->whereNotIn('user_id',$user_ids)->delete();
 
         foreach ($user_details as $user_detail) {
 
             $projectUsers = ProjectUser::where('project_id','=',$data->id)->where('user_id','=',$user_detail['id'])->first();
             $projectUsers = (!$projectUsers) ? new ProjectUser : $projectUsers;
             $projectUsers->project_id = $data->id;
             $projectUsers->user_id = $user_detail['id'];
             $projectUsers->save();
         }

 
        return response(['success' => 1, 'message' => $data->title.'  has been updated!']);
    }

    public function updateBulk()
    {
        $ids = request()->ids;
        $fields = request()->fields;

        $count = 0;
        foreach ($ids as $id) {

            $data = Project::find($id);

            if (!$data) {
                continue;
            }

            $count++;

            foreach ($fields as $key => $value) {
                if (!empty($fields[$key]) || !is_null($fields[$key])) {
                    $data->{$key} = $fields[$key];
                }
            }

            $data->save();
        }

        return response(['success' => 1, 'message' => $count.'  projects has been updated!']);
    }
    
    public function deleteBulk()
    {
        $ids = request()->ids;
        $count = 0;
        foreach ($ids as $id) {
            $project = Project::find($id);

            if (!$project)  {
                continue;
            }

            $count++;
            $project->delete();
        }

        return response(['success' => 1, 'message' => $count.' projects  has been deleted!']);
    }

    public function tagComplete($id)
    {
        $data = Project::find($id);
        $data->project_completed_date = date("Y-m-d");
        $data->save();
 
        return response(['success' => 1, 'message' => 'updated!']);
    }

    public function tagUncomplete($id)
    {
        $data = Project::find($id);
        $data->project_completed_date = null;
        $data->save();
 
        return response(['success' => 1, 'message' => 'updated!']);
    }
}
