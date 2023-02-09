<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller; 

use Auth; 
use Hash;
use Mail;
use Str;

use Illuminate\Http\Request; 

use App\Models\User;
use App\Models\Effort;


class EffortController extends Controller
{

    public function index()
    {
        $keyword = request()->keyword;

        $rows = Effort::where('id','>',0);

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
            return response(['success' => 0, 'message' => 'Please enter a effort title...'],500);
        }

        $new = new Effort;
        $new->title = $title;

        $new->save();

        return response(['success' => 1, 'message' => 'New effort has been added!']);
    }

    public function update($id)
    {
        $title = request()->title;


        if (empty($title)) {
            return response(['success' => 0, 'message' => 'Please enter a title...'],500);
        }
        
        $data = Effort::find($id);
        $data->title = $title;

        $data->save();

 
        return response(['success' => 1, 'message' => $data->title.'  has been updated!']);
    }
   
}
