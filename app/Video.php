<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Video extends Model{

    protected $fillable = ["name", "path"];

    protected $hidden = ['created_at', 'updated_at'];

}
