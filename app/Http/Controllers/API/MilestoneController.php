<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\Milestone;
use App\Models\Task;
use App\Models\Project; 

use Log;

class MilestoneController extends Controller
{
    public function index()
    {
        $keyword = request()->keyword;

        $rows = Milestone::where('id','>',0)->with('Project')->with('Milestone');

        if (!empty($keyword)) {
            $rows = $rows->where('title','LIKE','%'.$keyword.'%');
        }

        $rows = $rows->get();

        return response(['success' => 1, 'rows' => $rows]);
    }

    public function chart()
    {
        $rows = Milestone::where('id','>',0)->with('Project')->with('Milestone')->get();
        return response(['success' => 1, 'rows' => $rows]);
    }

    public function updateDependencies($id)
    {

        $milestone = Milestone::find($id);
        
        $new_start =  request()->chart_start;
        $new_end =  request()->chart_end;
        
        $this->processDates($milestone,$new_start,$new_end);

        return response(['success' => 1]);
    }

    public function deleteBulk()
    {
        $ids = request()->ids;
        $count = 0;
        foreach ($ids as $id) {
            $milestone = Milestone::find($id);

            if (!$milestone)  {
                continue;
            }

            $count++;
            $milestone->delete();
        }

        return response(['success' => 1, 'message' => $count.' milestone  has been deleted!']);
    }

    private function processDates($milestone,$new_start,$new_end)
    {
        $diff = 0;
        $diff = date_diff(date_create($milestone->milestone_start_date), date_create($new_start));

        
        if ($diff->days > 0) {

            $diff = 0;
            $diff = date_diff(date_create($milestone->milestone_start_date), date_create($milestone->milestone_due_date));
                    

            $new_start = date('Y-m-d', strtotime($new_start));
            $new_end = date('Y-m-d', strtotime($new_start. ' +'.$diff->days.' days'));
        }

        $milestone->milestone_start_date = $new_start;
        $milestone->milestone_due_date = $new_end;
        $milestone->save();

        $pointed = Milestone::where('id','=',$milestone->depends_id)->first();
        $current = $milestone;

     

        $direct_relatives = [];
        $find_other_children_ids = [];
        $exclude_children_ids = [];
        /*
        while ($pointed) {
            
            array_push($find_other_children_ids,$pointed->id);
            array_push($exclude_children_ids,$pointed->id);

            $new_pointed = $this->findParent($pointed,$current);
            
            $current = $pointed;
            $pointed = $new_pointed;
        }
        */

        
        $ids = [];
        $total_ids = [];
        // $total_ids = $find_other_children_ids;
        $message = '';
        $children = Milestone::where('depends_id','=',$milestone->id)->orWhereIn('depends_id',$find_other_children_ids)->orderBy('milestone_due_date','DESC')->get();
        $has_child = (count($children) > 0) ? true : false;

        while ($has_child) {

            foreach ($children as $index => $child) {
                Log::info('@@@ MILESTONE ===== '.$child->title);
                $ids = $this->findChildren($child);

                if (count($ids) > 0) {
                    $total_ids = $ids;
                }
            }


            $children = Milestone::whereIn('depends_id',$total_ids)->whereNotIn('id',$exclude_children_ids)->orderBy('milestone_due_date','DESC')->get();
            $has_child = (count($children) > 0) ? true : false;
            $total_ids = []; // clear
            
        }
    }

    public function findParent($milestone,$parent) {

        $diff = 0;
        $diff = date_diff(date_create($milestone->milestone_start_date), date_create($milestone->milestone_due_date));
                
        
        Log::info('START '.$milestone->milestone_start_date);
        Log::info('END '.$milestone->milestone_due_date);

        $new_end = date('Y-m-d', strtotime($parent->milestone_start_date. ' -1 days'));
        $new_start = date('Y-m-d', strtotime($new_end. ' -'.$diff->days.' days'));
        
        Log::info('NEW START '.$new_start);
        Log::info('NEW END '.$new_end);

        $milestone->milestone_start_date = $new_start;
        $milestone->milestone_due_date = $new_end;
        $milestone->save();
        
        $parent = Milestone::where('id','=',$milestone->depends_id)->first();
    
        return $parent;
    }

    public function findChildren($milestone) {
    
        $ids = [];
        $children = Milestone::where('depends_id','=',$milestone->id)->orderBy('milestone_due_date','DESC')->get();
        
        Log::info('CHILD '.$milestone->title);

        // get the range of the bar (milestone)
        $diff = 0;
        $diff = date_diff(date_create($milestone->milestone_start_date), date_create($milestone->milestone_due_date));
        
        $parent = Milestone::where('id','=',$milestone->depends_id)->first();

        $new_start = date('Y-m-d', strtotime($parent->milestone_due_date. ' +1 days'));
        $new_end = date('Y-m-d', strtotime($new_start. ' +'.$diff->days.' days'));

        $milestone->milestone_start_date = $new_start;
        $milestone->milestone_due_date = $new_end;
        $milestone->save(); 

        $milestone = Milestone::find($milestone->id);

        foreach ($children as $child) {
            array_push($ids,$child->id);
            
            Log::info('CHILD '.$child->title);


            // get the range of the bar (milestone)
            $diff = 0;
            $diff = date_diff(date_create($child->milestone_start_date), date_create($child->milestone_due_date));
    
            
            Log::info('START '.$child->milestone_start_date);
            Log::info('END '.$child->milestone_due_date);
            Log::info('FROM '.$milestone->milestone_due_date.' ADD '.$milestone->milestone_due_date. ' +1 days');
            

            $new_start = date('Y-m-d', strtotime($milestone->milestone_due_date. ' +1 days'));
            $new_end = date('Y-m-d', strtotime($new_start. ' +'.$diff->days.' days'));
            
            Log::info('NEW START '.$new_start);
            Log::info('NEW END '.$new_end);
            Log::info('BFF '.$new_start. ' +'.$diff->days.' days');
            

            Log::info('############################################');

            $child->milestone_start_date = $new_start;
            $child->milestone_due_date = $new_end;
            $child->save();
        }

    
        return $ids;
    }


    public function store()
    {
        $title = request()->title;
        $project_id = request()->project_id;
        $milestone_start_date = request()->milestone_start_date;
        $milestone_due_date = request()->milestone_due_date;
        $depends_id = request()->depends_id;

        $diff = 0;
        $diff = date_diff(date_create($milestone_start_date), date_create($milestone_due_date));

        
        if ($diff->invert) {
            return response(['success' => 0, 'message' => 'The start date must not be greater than due date'],500);
        }

        if (empty($title)) {
            return response(['success' => 0, 'message' => 'Please enter a title...', 'index' => 1],500);
        }

        // if (empty($milestone_start_date)) {
        //     return response(['success' => 0, 'message' => 'Please set a start date...', 'index' => 2],500);
        // }

        // if (empty($milestone_due_date)) {
        //     return response(['success' => 0, 'message' => 'Please set a due date...', 'index' => 3],500);
        // }
        

        $new = new Milestone;
        $new->title = $title;
        $new->milestone_start_date = $milestone_start_date;
        $new->project_id = $project_id;
        $new->milestone_due_date = $milestone_due_date;
        $new->depends_id = $depends_id;
        $new->project_id = $project_id;
        $new->save();

        return response(['success' => 1, 'message' => 'New Milestone has been added!', 'data' => $new]);
    }

    public function getDuration($id)
    {

        $duration = Task::where('milestone_id',$id)->sum('task_duration');

        return response(['success' => 1, 'rows' => $duration]);
    
    }

    public function update($id)
    {
        $title = request()->title;
        $project_id = request()->project_id;
        $milestone_start_date = request()->milestone_start_date;
        $milestone_due_date = request()->milestone_due_date;
        $depends_id = request()->depends_id;

     
        $diff = 0;
        $diff = date_diff(date_create($milestone_start_date), date_create($milestone_due_date));

        if ($diff->invert) {
            return response(['success' => 0, 'message' => 'The start date must not be greater than due date'],500);
        }

        if (empty($title)) {
            return response(['success' => 0, 'message' => 'Please enter a title...', 'index' => 1],500);
        }

        // if (empty($milestone_start_date)) {
        //     return response(['success' => 0, 'message' => 'Please set a start date...', 'index' => 2],500);
        // }

        // if (empty($milestone_due_date)) {
        //     return response(['success' => 0, 'message' => 'Please set a due date...', 'index' => 3],500);
        // }
        
        $data = Milestone::find($id);
        $data->project_id = $project_id;
        $data->title = $title;
        $data->milestone_start_date = $milestone_start_date;
        

        $childList = Milestone::select('id')->where('depends_id','=',$id)->get()->makeHidden('attribute')->toArray();

        if (!empty($childList)) {
            // var_dump('sulod');
            // die();
            foreach ($childList as $child) {
                $child = (object) $child;
                // var_dump($child->id);
                // die();
                $childTest = Milestone::find($child->id);
                $childTest->milestone_start_date = date('Y-m-d', strtotime( $milestone_due_date. ' +' . 1 .' days'));
                $total_task_duration = Task::where('milestone_id',$child->id)->sum('task_duration');
                $childTest->milestone_due_date = date('Y-m-d', strtotime( $milestone_due_date. ' +' . $total_task_duration .' days'));
                $childTest->save();
            }
        }

        /*
        if (!empty($depends_id)) {
            $dependMilestone = Milestone::find($depends_id);

            $data->milestone_start_date = date('Y-m-d', strtotime( $dependMilestone->milestone_due_date. ' +' . 1 .' days'));
            $total_task_duration = Task::where('milestone_id',$dependMilestone->id)->sum('task_duration');
            $data->milestone_due_date = date('Y-m-d', strtotime( $dependMilestone->milestone_due_date. ' +' . $total_task_duration .' days'));

        }else{
            $data->milestone_start_date = $milestone_start_date;
            $data->milestone_due_date = $milestone_due_date;
        }
        */


        $data->depends_id = $depends_id;
        $data->save();

        $milestone = Milestone::find($id);

        $new_start = request()->milestone_start_date;
        $new_end = request()->milestone_due_date;
        
        $this->processDates($milestone,$new_start,$new_end);

        return response(['success' => 1, 'message' => $data->title.'  has been updated!']);
    }

    public function tagComplete($id)
    {
        $data = Milestone::find($id);
        $data->milestone_completed_date = date("Y-m-d");
        $data->save();
 
        return response(['success' => 1, 'message' => 'updated!']);
    }

    public function tagUncomplete($id)
    {
        $data = Milestone::find($id);
        $data->milestone_completed_date = null;
        $data->save();
 
        return response(['success' => 1, 'message' => 'updated!']);
    }

    public function duplicate()
    {
        $project_id = request()->project_id;
        $project_id = $project_id['id'];
        $milestone_ids = request()->milestone_ids;

        $project = Project::find($project_id);

        if (!$project) {
            return response(['success' => 0, 'message' => 'Could not find the project...'],500);
        }

        $new_milestone_ids = [];

        foreach ($milestone_ids as $id) {
            $find  = Milestone::find($id);

            $new = new Milestone;

            $new->board_id = null;
            $new->project_id = $project->id;

            $new->title = $find->title;
            $new->milestone_start_date = $find->milestone_start_date;
            $new->milestone_due_date = $find->milestone_due_date;
            $new->depends_id = null;
            $new->save();

            $new_milestone_ids[] = $new->id;

        }

        $rows = Milestone::whereIn('id',$new_milestone_ids)->get();
        return response(['success' => 1, 'message' => 'Milestones has been duplicated!', 'rows' => $rows]);
    }

    public function updateBulk()
    {
        $ids = request()->ids;
        $fields = request()->fields;

        $count = 0;
        foreach ($ids as $id) {
            
            $milestone_start_date = $fields['milestone_start_date'];
            $milestone_due_date = $fields['milestone_due_date'];
            $project_id = $fields['project_id'];


            $data = Milestone::find($id);

            if (!$data) {
                continue;
            }

            $count++;

            if (!empty($milestone_start_date) || !is_null($milestone_start_date)) {
                $data->milestone_start_date = $milestone_start_date;
            }

            if (!empty($milestone_due_date) || !is_null($milestone_due_date)) {
                $data->milestone_due_date = $milestone_due_date;
            }

            if (!empty($project_id) || !is_null($project_id)) {
                $data->project_id = $project_id;
            }

            $data->save();
        }

        return response(['success' => 1, 'message' => $count.'  milestones has been updated!']);
    }
}