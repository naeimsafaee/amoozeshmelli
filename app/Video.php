<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Video extends Model{

    protected $fillable = ["name", "path"];

    protected $hidden = ['created_at', 'updated_at' , "name" , "path"];

    protected $appends = ["url"];

    public function getUrlAttribute(){
        return $this->path . "/" . $this->name;
    }

}
