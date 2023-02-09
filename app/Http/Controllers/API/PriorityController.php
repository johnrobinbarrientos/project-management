<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller; 

use Auth; 
use Hash;
use Mail;
use Str;

use Illuminate\Http\Request; 

use App\Models\User;
use App\Models\Priority;


class PriorityController extends Controller
{

    public function index()
    {
        $keyword = request()->keyword;

        $rows = Priority::where('id','>',0);

        if (!empty($keyword)) {
            $rows = $rows->where('title','LIKE','%'.$keyword.'%');
        }

        $rows = $rows->get();

        return response(['success' => 1, 'rows' => $rows]);
    }


    public function store()
    {
        $title = request()->title;

        if (empty($title)) {
            return response(['success' => 0, 'message' => 'Please enter a priority title...'],500);
        }

        $new = new Priority;
        $new->title = $title;

        $new->save();

        return response(['success' => 1, 'message' => 'New priority has been added!']);
    }

    public function update($id)
    {
        $title = request()->title;


        if (empty($title)) {
            return response(['success' => 0, 'message' => 'Please enter a title...'],500);
        }
        
        $data = Priority::find($id);
        $data->title = $title;

        $data->save();

 
        return response(['success' => 1, 'message' => $data->title.'  has been updated!']);
    }
   
}
