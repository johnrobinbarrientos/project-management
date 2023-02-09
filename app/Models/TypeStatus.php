<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TypeStatus extends Model
{
    use SoftDeletes;
    
    protected $table = 'type_status';

    protected $fillable = [];

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

   
}