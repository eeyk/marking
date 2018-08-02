<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Weight extends Model
{
    use SoftDeletes;
    protected $table = 'weights';

    protected $dates = ['deleted_at'];

    protected $fillable = ['activity_id','weight','level','levelNums'];
}
