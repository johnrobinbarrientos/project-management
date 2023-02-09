<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\Department; 

class DepartmentController extends Controller
{
    public function index()
    {
        $keyword = request()->keyword;

        $rows = Department::where('id','>',0);

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
            return response(['success' => 0, 'message' => 'Please enter a department title...'],500);
        }

        $new = new Department;
        $new->title = $title;
        $new->save();

        return response(['success' => 1, 'message' => 'New Department has been added!']);
    }

    public function update($id)
    {
        $title = request()->title;

        if (empty($title)) {
            return response(['success' => 0, 'message' => 'Please enter a department title...'],500);
        }
        
        $data = Department::find($id);
        $data->title = $title;
        $data->save();

 
        return response(['success' => 1, 'message' => $data->title.'  has been updated!']);
    }
}