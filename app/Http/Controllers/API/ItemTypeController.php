<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\ItemType; 

class ItemTypeController extends Controller
{
    public function index()
    {
        $keyword = request()->keyword;

        $rows = ItemType::where('id','>',0);

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
            return response(['success' => 0, 'message' => 'Please enter a item type title...'],500);
        }

        $new = new ItemType;
        $new->title = $title;
        $new->save();

        return response(['success' => 1, 'message' => 'New item type has been added!']);
    }

    public function update($id)
    {
        $title = request()->title;

        if (empty($title)) {
            return response(['success' => 0, 'message' => 'Please enter a item type  title...'],500);
        }
        
        $data = ItemType::find($id);
        $data->title = $title;
        $data->save();

 
        return response(['success' => 1, 'message' => $data->title.'  has been updated!']);
    }
}