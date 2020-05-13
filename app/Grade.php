<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grade extends Model
{
//    use SoftDeletes;

    protected $fillable = ['title'];
    protected $hidden = ['created_at' , 'updated_at', 'deleted_at'];

}
