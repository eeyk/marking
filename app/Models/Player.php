<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Player extends Model
{
    use SoftDeletes;

    protected $table = 'players';

    protected $fillable = ['name', 'details', 'score','activity_id','group','isMarking','img'];

    protected $dates = ['deleted_at'];

}
