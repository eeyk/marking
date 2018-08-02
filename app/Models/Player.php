<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Player extends Model
{
    use SoftDeletes;

    protected $table = 'players';

    protected $fillable = ['name', 'details', 'activity_id','url','score','group','groupName'];

    protected $dates = ['deleted_at'];

}
