<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemType extends Model
{
    use SoftDeletes;
    
    protected $table = 'item_type';

    protected $fillable = [];

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

   
}