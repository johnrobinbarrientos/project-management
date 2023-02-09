<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_groups';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public function GroupUsers(){
        return $this->hasMany('App\Models\UserGroupDetails','user_group_id','id')->with('User')->with('Group');
    }

    public function Users(){
        return $this->hasMany('App\Models\UserGroupDetails','user_group_id','id')->with('User');
    }
   


}