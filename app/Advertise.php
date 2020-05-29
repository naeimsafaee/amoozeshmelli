<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Advertise extends Model{

    protected $fillable = ['title' , 'video_id' , 'gift' , 'count' , 'price'];
    protected $hidden = ['created_at', 'updated_at'];

    public function video(){
        return $this->hasOne(Video::class, 'id', 'video_id');
    }

}
