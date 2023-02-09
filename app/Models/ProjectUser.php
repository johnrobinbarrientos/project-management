<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectUser extends Model
{
    use SoftDeletes;
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'project_users';
    

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public function User(){
        return $this->belongsTo('App\Models\User','user_id','id');
    }

}