<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Advertise extends Model{

    protected $fillable = ['title' , 'video_id' , 'gift' , 'count' , 'price'];
}
