<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'boards';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public function tasks(){
        return $this->hasMany('App\Models\Task')->orderBy('sort_index');
    }

    public function projects(){
        return $this->hasMany('App\Models\Project')->orderBy('sort_index');
    }

    public function sprints(){
        return $this->hasMany('App\Models\Sprint')->orderBy('sort_index');
    }

    public function retrospectives(){
        return $this->hasMany('App\Models\Retrospective')->orderBy('sort_index');
    }

    public function milestones(){
        return $this->hasMany('App\Models\Milestone')->orderBy('sort_index');
    }
    
}