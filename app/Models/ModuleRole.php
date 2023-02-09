<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModuleRole extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'module_roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public function module(){
        return $this->belongsTo('App\Models\Module','module_id','id');
    }
}