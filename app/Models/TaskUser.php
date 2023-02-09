<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;

class TaskUser extends Model
{
    //use SoftDeletes;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'task_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public function User(){
        return $this->belongsTo('App\Models\User','user_id','id');
    }

    // public function Group(){
    //     return $this->belongsTo('App\Models\UserGroup','user_group_id','id');
    // }
    
}