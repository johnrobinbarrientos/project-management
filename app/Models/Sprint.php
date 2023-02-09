<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sprint extends Model
{
    use SoftDeletes;
    
    protected $table = 'sprints';

    protected $fillable = [];

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    public function Project(){
        return $this->belongsTo('App\Models\Project');
    }

    public function Sprint(){
        return $this->belongsTo('App\Models\Sprint','depends_id','id');
    }
   
}