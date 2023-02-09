<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'notifications';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public function user_who_fired_event()
    {
        return $this->belongsTo('App\Models\User', 'user_who_fired_event');
    }
}