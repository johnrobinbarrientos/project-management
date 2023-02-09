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


class BoardController extends Controller
{

    public function index()
    {
        $keyword = request()->keyword;

        $rows = Board::where('id','>',0)->with('tasks')->with('projects')->with('sprints')->with('retrospectives')->with('milestones');

        if (!empty($keyword)) {
            $rows = $rows->where('title','LIKE','%'.$keyword.'%');
        }

        $rows = $rows->orderBy('order');
        $rows = $rows->get();

        return response(['success' => 1, 'rows' => $rows]);
    }


    public function store()
    {
        $title = request()->title;
        $description = request()->description;
        $due_date = request()->due_date;
        $module_id = request()->module_id;

        if (empty($title)) {
            return response(['success' => 0, 'message' => 'Please enter a task title...'],500);
        }

        if (empty($due_date)) {
            return response(['success' => 0, 'message' => 'Please set a due date...'],500);
        }

        if (strlen($description) < 5) {
            return response(['success' => 0, 'message' => 'Task descritpion is too short...'],500);
        }

        $board = Board::where('module_id','=',$module_id)->where('is_default','=',true)->first();

        if (!$board) {
            return response(['success' => 0, 'message' => 'Please setup a default board for module...'],500);
        }

        $new = new Task;
        $new->title = $title;
        $new->description = $description;
        $new->due_date = $due_date;
        $new->board_id = $board->id;
        $new->save();

        return response(['success' => 1, 'message' => 'New task has been added!']);
    }

    public function update($id)
    {
        $title = request()->title;
        $description = request()->description;
        $due_date = request()->due_date;

        if (empty($title)) {
            return response(['success' => 0, 'message' => 'Please enter a task title...'],500);
        }

        if (empty($due_date)) {
            return response(['success' => 0, 'message' => 'Please set a due date...'],500);
        }

        if (strlen($description) < 5) {
            return response(['success' => 0, 'message' => 'Task descritpion is too short...'],500);
        }

        
        $data = Task::find($id);
        $data->title = $title;
        $data->due_date = $due_date;
        $data->description = $description;
        $data->save();

 
        return response(['success' => 1, 'message' => $data->title.'  has been updated!']);
    }
   

    public function sortTasks($board_id)
    {
        $tasks = request()->tasks;
        $cloned_tasks_id = request()->cloned_tasks_id;

        $board = Board::find($board_id);
        $count = 0;
        foreach ($tasks as $task) {
            $task = (object) $task;
            
            if ($task->id == $cloned_tasks_id) {
                $find  = Task::find($task->id);

                $new = new Task;

                $new->board_id = $board->id;
                $new->project_id = $board->project_id;

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

            } else {
                $find  = Task::find($task->id);
                $find->board_id = $board_id;
                $find->sort_index = $count;
                $find->save();
            }

            

            $count++;
        }

        return response(['success' => 1, 'message' => 'sorted!']);
    }
}
