<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Milestone extends Model
{
    use SoftDeletes;
    
    protected $table = 'milestones';

    protected $fillable = [];

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;


    public function Project(){
        return $this->belongsTo('App\Models\Project');
    }

    public function Task(){
        return $this->hasMany('App\Models\Task','milestone_id','id');
    }

    public function Milestone(){
        return $this->belongsTo('App\Models\Milestone','depends_id','id');
    }
   
}