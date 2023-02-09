<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'projects';
    

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public function ProjectUsers(){
        return $this->hasMany('App\Models\ProjectUser','project_id','id')->with('User');
    }

}