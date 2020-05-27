<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PeopleToPercent extends Model{

    protected $fillable = ['percent' , 'people_id' , 'product_id' , 'section_id' , 'quiz_id' , 'advertise_id'];
    protected $hidden = ['created_at', 'updated_at'];

    public function people(){
        return $this->hasOne(People::class, 'id', 'people_id');
    }

}
