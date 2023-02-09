<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller; 

use Auth; 
use Hash;
use Mail;
use Str;

use Illuminate\Http\Request; 

use App\Models\User;
use App\Models\Board;
use App\Models\Task;
use App\Models\TaskTag;
use App\Models\TaskUser;
use App\Models\TaskUserGroups;
use App\Models\Project;


class TaskController extends Controller
{

    public function index()
    {
        $keyword = request()->keyword;

        $rows = Task::where('id','>',0)->with('tags.tag')->with('Project')->with('Milestone')->with('Task')->with('Department')->with('Sprint')->with('TaskUsers')->with('TaskUserGroups');

        if (request()->project_slug) {
            $project = Project::where('slug','=',request()->project_slug)->first();
            $rows = ($project) ? $rows->where('project_id','=',$project->id) : $rows;
        }
        
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

        $rows = $rows->orderBy('sort_index');
        $rows = $rows->get();

        return response(['success' => 1, 'rows' => $rows]);
    }

    /*
    public function updateDependencies($id)
    {


        $parent = Task::find($id);
        $parent->task_start_date = request()->chart_start;
        $parent->task_due_date = request()->chart_end;
        $parent->save();

        $rows = Task::where('parent_task_id', $id)->get();

        foreach ($rows as $row) {
            
            $diff = 0;

            $dependent = Task::find($row->id);
            

            $diff = date_diff(date_create($row->task_start_date), date_create($row->task_due_date));
            
            $dependent->task_start_date = request()->dependent_chart_start;
            $dependent->task_due_date = date('Y-m-d', strtotime(request()->dependent_chart_start. ' +' . $diff->format("%a") .' days'));
            $dependent->save();
        }

        return response(['success' => 1, 'rows' => $rows]);
    }
    */

    public function updateDependencies($id)
    {

        $task = Task::find($id);
        
        $new_start = request()->chart_start;
        $new_end = request()->chart_end;
        
        $this->processDates($task,$new_start,$new_end);

        return response(['success' => 1]);
    }

    private function processDates($task,$new_start,$new_end)
    {
        $diff = 0;
        $diff = date_diff(date_create($task->task_start_date), date_create($new_start));


        if ($diff->days > 0) {

            $diff = 0;
            $diff = date_diff(date_create($task->task_start_date), date_create($task->task_due_date));
                    

            $new_start = date('Y-m-d', strtotime($new_start));
            $new_end = date('Y-m-d', strtotime($new_start. ' +'.$diff->days.' days'));
        }

        $task->task_start_date = $new_start;
        $task->task_due_date = $new_end;
        $task->save();

        $pointed = Task::where('id','=',$task->parent_task_id)->first();
        $current = $task;

     

        $direct_relatives = [];
        $find_other_children_ids = [];
        $exclude_children_ids = [];
  

        
        $ids = [];
        $total_ids = [];
        // $total_ids = $find_other_children_ids;
        $message = '';
        $children = Task::where('parent_task_id','=',$task->id)->orWhereIn('parent_task_id',$find_other_children_ids)->orderBy('task_due_date','DESC')->get();
        $has_child = (count($children) > 0) ? true : false;

        while ($has_child) {

            foreach ($children as $index => $child) {
                $ids = $this->findChildren($child);

                if (count($ids) > 0) {
                    $total_ids = $ids;
                }
            }


            $children = Task::whereIn('parent_task_id',$total_ids)->whereNotIn('id',$exclude_children_ids)->orderBy('task_due_date','DESC')->get();
            $has_child = (count($children) > 0) ? true : false;
            $total_ids = []; // clear
            
        }
    }

    public function findChildren($task) {
    
        $ids = [];
        $children = Task::where('parent_task_id','=',$task->id)->orderBy('task_due_date','DESC')->get();

        // get the range of the bar (milestone)
        $diff = 0;
        $diff = date_diff(date_create($task->task_start_date), date_create($task->task_due_date));
        
        $parent = Task::where('id','=',$task->parent_task_id)->first();

        $new_start = date('Y-m-d', strtotime($parent->task_due_date. ' +1 days'));
        $new_end = date('Y-m-d', strtotime($new_start. ' +'.$diff->days.' days'));

        $task->task_start_date = $new_start;
        $task->task_due_date = $new_end;
        $task->save(); 

        $task = Task::find($task->id);

        foreach ($children as $child) {
            array_push($ids,$child->id);
            
            // get the range of the bar (milestone)
            $diff = 0;
            $diff = date_diff(date_create($child->task_start_date), date_create($child->task_due_date));
    
            $new_start = date('Y-m-d', strtotime($task->task_due_date. ' +1 days'));
            $new_end = date('Y-m-d', strtotime($new_start. ' +'.$diff->days.' days'));
            


            $child->task_start_date = $new_start;
            $child->task_due_date = $new_end;
            $child->save();
        }

    
        return $ids;
    }

    public function chart()
    {

        $rows = Task::where('id','>',0)->with('Task')->get();

        return response(['success' => 1, 'rows' => $rows]);
    }


    public function store()
    {
        $title = request()->title;
        $description = request()->description;
        $task_start_date = request()->task_start_date;
        $task_due_date = request()->task_due_date;
        $task_due_date = request()->task_due_date;
        $task_duration = request()->task_duration;
        $module_id = request()->module_id;
        $milestone_id = request()->milestone_id;
        $project_id = request()->project_id;
        $parent_task_id = request()->parent_task_id;
        $dept_id = request()->dept_id;
        $sprint_id = request()->sprint_id;
        $note = request()->note;


        $user_details = (is_array(request()->user_details)) ? request()->user_details : [];
        
        $tags = request()->tags;
        $tags = (is_array($tags)) ? $tags : [];

        $user_group_details = (is_array(request()->user_group_details)) ? request()->user_group_details : [];

        if (empty($title)) {
            return response(['success' => 0, 'message' => 'Please enter a task title...'],500);
        }

        if (empty($task_due_date)) {
            return response(['success' => 0, 'message' => 'Please set a due date...'],500);
        }

        if (strlen($description) < 5) {
            return response(['success' => 0, 'message' => 'Task descritpion is too short...'],500);
        }

        // $board = Board::where('module_id','=',$module_id)->where('is_default','=',true)->first();

        // if (!$board) {
        //     return response(['success' => 0, 'message' => 'Please setup a default board for module...'],500);
        // }

            
        $new = new Task;
        $new->title = $title;
        $new->description = $description;
        $new->task_start_date = $task_start_date;
        $new->task_due_date = $task_due_date;
        $new->task_duration = $task_duration;
        // $new->board_id = $board->id;
        $new->milestone_id = $milestone_id;
        $new->project_id = $project_id;
        $new->parent_task_id = $parent_task_id;
        $new->dept_id = $dept_id;
        $new->sprint_id = $sprint_id;
        $new->note = $note;
        $new->save();

        //*********************** Add Task Users/
        foreach ($user_details as $user_detail) {
            $taskUsers = TaskUser::where('task_id','=',$new->id)->where('user_id','=',$user_detail['id'])->first();
            $taskUsers = (!$taskUsers) ? new TaskUser : $taskUsers;
            $taskUsers->task_id = $new->id;
            $taskUsers->user_id = $user_detail['id'];
            $taskUsers->save();
        }

        //*********************** Add Task User Group/
        foreach ($user_group_details as $user_group) {

            $taskUserGroups = TaskUserGroups::where('task_id','=',$new->id)->where('user_group_id','=',$user_group['id'])->first();
            $taskUserGroups = (!$taskUserGroups) ? new TaskUserGroups : $taskUserGroups;
            $taskUserGroups->task_id = $new->id;
            $taskUserGroups->user_group_id = $user_group['id'];
            $taskUserGroups->save();
        }


        foreach ($tags as $tag) {
            $task_tag = TaskTag::where('task_id','=',$new->id)->where('tag_id','=',$tag['id'])->first();
            $task_tag = (!$task_tag) ? new TaskTag : $task_tag;
            $task_tag->task_id = $new->id;
            $task_tag->tag_id = $tag['id'];
            $task_tag->save();
        }

        return response(['success' => 1, 'message' => 'New task has been added!']);
    }

    public function update($id)
    {
        $title = request()->title;
        $description = request()->description;
        $task_start_date = request()->task_start_date;
        $task_due_date = request()->task_due_date;
        $task_duration = request()->task_duration;
        $milestone_id = request()->milestone_id;
        $project_id = request()->project_id;
        $parent_task_id = request()->parent_task_id;
        $dept_id = request()->dept_id;
        $sprint_id = request()->sprint_id;
        $note = request()->note;

        $user_details = (is_array(request()->user_details)) ?  request()->user_details : [];
        $user_group_details = (is_array(request()->user_group_details)) ?  request()->user_group_details : [];
        $tags = (is_array(request()->tags)) ?  request()->tags : [];


        $diff = 0;
        $diff = date_diff(date_create($task_start_date), date_create($task_due_date));

        if ($diff->invert) {
            return response(['success' => 0, 'message' => 'The start date must not be greater than due date'],500);
        }
        
        if (empty($title)) {
            return response(['success' => 0, 'message' => 'Please enter a task title...'],500);
        }

        if (strlen($description) < 5) {
            return response(['success' => 0, 'message' => 'Task descritpion is too short...'],500);
        }

        
        $data = Task::find($id);
        $data->title = $title;
        $data->description = $description;
        $data->task_start_date = $task_start_date;
        $data->task_due_date = $task_due_date;
        $data->task_duration = $task_duration;
        $data->milestone_id = $milestone_id;
        $data->project_id = $project_id;
        $data->parent_task_id = $parent_task_id;
        $data->dept_id = $dept_id;
        $data->sprint_id = $sprint_id;
        $data->note = $note;
        $data->save();


        //*********************** Updating Task Users/
        $user_ids = [];
        
        foreach ($user_details as $key => $detail) {
            $user_ids[] = $detail['id'];
        }

        // delete details
        TaskUser::where('task_id','=',$data->id)->whereNotIn('user_id',$user_ids)->delete();

        foreach ($user_details as $user_detail) {

            $taskUsers = TaskUser::where('task_id','=',$data->id)->where('user_id','=',$user_detail['id'])->first();
            $taskUsers = (!$taskUsers) ? new TaskUser : $taskUsers;
            $taskUsers->task_id = $data->id;
            $taskUsers->user_id = $user_detail['id'];
            $taskUsers->save();
        }


        //*********************** Updating Task User Group/
        $user_group_ids = [];
        
        foreach ($user_group_details as $key => $detail) {
            $user_group_ids[] = $detail['id'];
        }

        // delete details
        TaskUserGroups::where('task_id','=',$data->id)->whereNotIn('user_group_id',$user_group_ids)->delete();

        foreach ($user_group_details as $user_group) {

            $taskUserGroups = TaskUserGroups::where('task_id','=',$data->id)->where('user_group_id','=',$user_group['id'])->first();
            $taskUserGroups = (!$taskUserGroups) ? new TaskUserGroups : $taskUserGroups;
            $taskUserGroups->task_id = $data->id;
            $taskUserGroups->user_group_id = $user_group['id'];
            $taskUserGroups->save();
        }


        $tag_ids = [];
        
        foreach ($tags as $key => $value) {
            $tag_ids[] = $value['id'];
        }


        $delete = TaskTag::where('task_id','=',$data->id)->whereNotIn('tag_id',$tag_ids)->delete();

        foreach ($tags as $tag) {
            $task_tag = TaskTag::where('task_id','=',$data->id)->where('tag_id','=',$tag['id'])->first();
            $task_tag = (!$task_tag) ? new TaskTag : $task_tag;
            $task_tag->task_id = $data->id;
            $task_tag->tag_id = $tag['id'];
            $task_tag->save();
        }

        $row = Task::where('id','=',$id)->with('tags.tag')->with('Project')->with('Milestone')->with('Task')->with('Department')->with('Sprint')->with('TaskUsers')->with('TaskUserGroups')->first();
 
        return response(['success' => 1, 'message' => $data->title.'  has been updated!', 'data' => $row]);
    }

    public function duplicate()
    {
        $project_id = request()->project_id;
        $project_id = $project_id['id'];
        $task_ids = request()->task_ids;

        $project = Project::find($project_id);

        if (!$project) {
            return response(['success' => 0, 'message' => 'Could not find the project...'],500);
        }

        $new_task_ids = [];

        foreach ($task_ids as $id) {
            $find  = Task::find($id);

            $new = new Task;

            $new->board_id = null;
            $new->project_id = $project->id;

            $new->title = $find->title;
            $new->description = $find->description;
            $new->task_start_date = $find->task_start_date;
            $new->task_due_date = $find->task_due_date;
            $new->task_duration = $find->task_duration;
            $new->milestone_id = $find->milestone_id;
            $new->parent_task_id = null;
            $new->dept_id = $find->dept_id;
            $new->sprint_id = $find->sprint_id;
            $new->note = $find->note;
            $new->save();

            $new_task_ids[] = $new->id;

        }

        $rows = Task::whereIn('id',$new_task_ids)->with('tags.tag')->with('Project')->with('Milestone')->with('Task')->with('Department')->with('Sprint')->with('TaskUsers')->with('TaskUserGroups')->get();
        return response(['success' => 1, 'message' => 'Tasks has been duplicated!', 'rows' => $rows]);
    }
    
    public function updateBulk()
    {
        $ids = request()->ids;
        $fields = request()->fields;

        $count = 0;
        foreach ($ids as $id) {
            
            $task_start_date = $fields['task_start_date'];
            $task_due_date = $fields['task_due_date'];
            $milestone_id = $fields['milestone_id'];


            $data = Task::find($id);

            if (!$data) {
                continue;
            }

            $count++;

            if (!empty($task_start_date) || !is_null($task_start_date)) {
                $data->task_start_date = $task_start_date;
            }

            if (!empty($task_due_date) || !is_null($task_due_date)) {
                $data->task_due_date = $task_due_date;
            }

            if (!empty($milestone_id) || !is_null($milestone_id)) {
                $data->milestone_id = $milestone_id;
            }

            $data->save();
        }

        return response(['success' => 1, 'message' => $count.'  tasks has been updated!']);
    }


    public function deleteBulk()
    {
        $ids = request()->ids;
        $count = 0;
        foreach ($ids as $id) {
            $task = Task::find($id);

            if (!$task)  {
                continue;
            }

            $count++;
            $task->delete();
        }

        return response(['success' => 1, 'message' => $count.' tasks  has been deleted!']);
    }

    public function tag()
    {
        
    }
}
