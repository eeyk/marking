<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use SoftDeletes;

    protected $table = 'activities';

    protected $fillable = ['name', 'details', 'usersNum','playersNum'];

    protected $dates = ['deleted_at'];
}
