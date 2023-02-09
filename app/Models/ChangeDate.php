<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChangeDate extends Model
{
    use SoftDeletes;
    
    protected $table = 'change_dates';

    protected $fillable = [];

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

   
}