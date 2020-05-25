<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Control extends Model{


    public function image(){
        return $this->hasOne(Image::class, 'id', 'option');
    }

}
