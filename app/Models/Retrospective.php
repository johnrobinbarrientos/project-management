<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Retrospective extends Model
{
    use SoftDeletes;
    
    protected $table = 'retrospectives';

    protected $fillable = [];

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    public function Milestone(){
        return $this->belongsTo('App\Models\Milestone');
    }

    public function Sprint(){
        return $this->belongsTo('App\Models\Sprint');
    }

    public function Project(){
        return $this->belongsTo('App\Models\Project');
    }

    public function Status(){
        return $this->belongsTo('App\Models\Status');
    }

   
}