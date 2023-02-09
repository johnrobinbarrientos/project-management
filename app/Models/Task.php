<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{

    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tasks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public function Milestone(){
        return $this->belongsTo('App\Models\Milestone');
    }

    public function Project(){
        return $this->belongsTo('App\Models\Project');
    }

    public function Task(){
        return $this->belongsTo('App\Models\Task','parent_task_id','id');
    }

    public function Department(){
        return $this->belongsTo('App\Models\Department');
    }

    public function Sprint(){
        return $this->belongsTo('App\Models\Sprint');
    }

    public function TaskUsers(){
        return $this->hasMany('App\Models\TaskUser','task_id','id')->with('User');
    }

    public function TaskUserGroups(){
        return $this->hasMany('App\Models\TaskUserGroups','task_id','id')->with('Group');
    }

    public function tags()
    {
        return $this->hasMany('App\Models\TaskTag')->with('tag');
    }
}