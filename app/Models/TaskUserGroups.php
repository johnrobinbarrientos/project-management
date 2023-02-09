<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskUserGroups extends Model
{
    use SoftDeletes;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'task_user_groups';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public function Group(){
        return $this->belongsTo('App\Models\UserGroup','user_group_id','id');
    }

    
}