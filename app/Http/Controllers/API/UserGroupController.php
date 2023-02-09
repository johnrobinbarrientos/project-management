<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller; 

use Auth; 
use Hash;
use Mail;
use Str;

use Illuminate\Http\Request; 

use App\Models\User;
use App\Models\UserGroup;
use App\Models\UserGroupDetails;


class UserGroupController extends Controller
{

    public function index()
    {
        $keyword = request()->keyword;

        $rows = UserGroup::where('id','>',0)->with('GroupUsers');

        if (!empty($keyword)) {
            $rows = $rows->where('title','LIKE','%'.$keyword.'%');
        }

        $rows = $rows->get();

        return response(['success' => 1, 'rows' => $rows]);
    }

    public function show($groupid)
    {
        $userGroup = UserGroup::where('id','=',$groupid)->with('Users')->first();
        return response()->json(['success' => 1, 'rows' => $userGroup], 200);
    }


    public function store()
    {
        $title = request()->title;
        $user_details = request()->user_details;

        if (empty($title)) {
            return response(['success' => 0, 'message' => 'Please enter title...'],500);
        }

        $new = new UserGroup;
        $new->title = $title;

        $new->save();

        foreach ($user_details as $user_detail) {

            $groupDetail = UserGroupDetails::where('user_group_id','=',$new->id)->where('user_id','=',$user_detail['id'])->first();
            $groupDetail = (!$groupDetail) ? new UserGroupDetails : $groupDetail;
            $groupDetail->user_group_id = $new->id;
            $groupDetail->user_id = $user_detail['id'];
            $groupDetail->save();
        }

        return response(['success' => 1, 'message' => 'New group has been added!']);
    }

    public function update($id)
    {
        $title = request()->title;
        $user_details = request()->user_details;

        if (empty($title)) {
            return response(['success' => 0, 'message' => 'Please enter a title...'],500);
        }
        
        $data = UserGroup::find($id);
        $data->title = $title;

        $data->save();


        $user_ids = [];
        
        foreach ($user_details as $key => $detail) {
            $user_ids[] = $detail['id'];
        }

        // delete details
        UserGroupDetails::where('user_group_id','=',$data->id)->whereNotIn('user_id',$user_ids)->delete();


        foreach ($user_details as $user_detail) {

            $groupDetail = UserGroupDetails::where('user_group_id','=',$data->id)->where('user_id','=',$user_detail['id'])->first();
            $groupDetail = (!$groupDetail) ? new UserGroupDetails : $groupDetail;
            $groupDetail->user_group_id = $data->id;
            $groupDetail->user_id = $user_detail['id'];
            $groupDetail->save();
        }

 
        return response(['success' => 1, 'message' => $data->title.'  has been updated!']);
    }
   
}
