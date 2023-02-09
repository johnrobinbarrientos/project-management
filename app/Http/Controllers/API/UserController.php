<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller; 

use Auth; 
use Hash;
use Mail;
use Str;

use Illuminate\Http\Request; 

use App\Models\User;
use App\Models\Role;


class UserController extends Controller
{

    public function index()
    {
        $keyword = request()->keyword;


        $rows = User::where('id','>',0)->with('UserType')->with('UserRole');

        if (!empty($keyword)) {
            $rows = $rows->where(function($query) use ($keyword) {
                $query->where('email','LIKE','%'.$keyword.'%')
                    ->orWhere('firstname','LIKE','%'.$keyword.'%')
                    ->orWhere('lastname','LIKE','%'.$keyword.'%');
            });
        }

        $rows = $rows->get();

        return response(['success' => 1, 'rows' => $rows]);
    }


    public function store()
    {
        $email = request()->email;
        $firstname = request()->firstname;
        $lastname = request()->lastname;
        $password_1 = request()->password_1;
        $password_2 = request()->password_2;
        $user_type_id = request()->user_type_id;
        $user_role_id = (isset(request()->user_role) && isset(request()->user_role['id'])) ? request()->user_role['id'] : null;
        $time = request()->time;

        if (empty($email)) {
            return response(['success' => 0, 'message' => 'Please enter a email address...'],500);
        }

        if (empty($firstname)) {
            return response(['success' => 0, 'message' => 'Please enter a first name...'],500);
        }

        if (empty($lastname)) {
            return response(['success' => 0, 'message' => 'Please enter a last name...'],500);
        }


        if ($password_1 != $password_2) {
            return response(['success' => 0, 'message' => 'Password not matched...'],500);
        }

        if (strlen($password_1) < 5) {
            return response(['success' => 0, 'message' => 'Password too short...'],500);
        }

        $new = new User;
        $new->email = $email;
        $new->firstname = $firstname;
        $new->lastname = $lastname;
        $new->user_type_id = $user_type_id;
        $new->role_id = $user_role_id;
        $new->password = app('hash')->make($password_1);
        $new->time = $time;
        $new->save();

        return response(['success' => 1, 'message' => 'New user has been added!']);
    }

    public function update($id)
    {
        $email = request()->email;
        $firstname = request()->firstname;
        $lastname = request()->lastname;
        $password_1 = request()->password_1;
        $password_2 = request()->password_2;
        $user_type_id = request()->user_type_id;
        $user_role_id = (isset(request()->user_role) && isset(request()->user_role['id'])) ? request()->user_role['id'] : null;
        $time = request()->time;

        if (empty($email)) {
            return response(['success' => 0, 'message' => 'Please enter a email address...'],500);
        }

        if (empty($firstname)) {
            return response(['success' => 0, 'message' => 'Please enter a first name...'],500);
        }

        if (empty($lastname)) {
            return response(['success' => 0, 'message' => 'Please enter a last name...'],500);
        }


        if ($password_1 != $password_2) {
            return response(['success' => 0, 'message' => 'Password not matched...'],500);
        }

        if (strlen($password_1) < 5) {
            return response(['success' => 0, 'message' => 'Password too short...'],500);
        }

        

        $data = User::find($id);
        $data->email = $email;
        $data->firstname = $firstname;
        $data->lastname = $lastname;
        $data->user_type_id = $user_type_id;
        $data->role_id = $user_role_id;
        $data->password = app('hash')->make($password_1);
        $data->time = $time;
        $data->save();

 

        return response(['success' => 1, 'message' => $data->first_name.' role has been updated!']);
    }
   
}
