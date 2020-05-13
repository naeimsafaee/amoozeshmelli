<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model{

//    use SoftDeletes;

    protected $fillable = ["title", 'lesson_id'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at', 'lesson_id'];
}
