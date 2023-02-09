<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller; 

use Auth; 
use Hash;
use Mail;
use Str;

use Illuminate\Http\Request; 

use App\Models\UserType;


class UserTypeController extends Controller
{

    public function index()
    {
        $keyword = request()->keyword;

        $rows = UserType::where('id','>',0);

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
            return response(['success' => 0, 'message' => 'Please enter a title...'],500);
        }

        $new = new UserType;
        $new->title = $title;

        $new->save();

        return response(['success' => 1, 'message' => 'New user type has been added!']);
    }

    public function update($id)
    {
        $title = request()->title;


        if (empty($title)) {
            return response(['success' => 0, 'message' => 'Please enter a title...'],500);
        }
        
        $data = UserType::find($id);
        $data->title = $title;

        $data->save();

 
        return response(['success' => 1, 'message' => $data->title.'  has been updated!']);
    }
   
}
