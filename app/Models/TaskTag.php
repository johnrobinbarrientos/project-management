<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskTag extends Model
{
    use SoftDeletes;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'task_tags';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public function Task(){
        return $this->belongsTo('App\Models\Task','task_id','id');
    }

    public function Tag(){
        return $this->belongsTo('App\Models\Tag','tag_id','id');
    }

    // public function Group(){
    //     return $this->belongsTo('App\Models\UserGroup','user_group_id','id');
    // }
    
}