<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = ["user_id" , "is_global"];
    protected $hidden = ['created_at' , 'updated_at' , 'deleted_at' , 'user_id'];

    public function info(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }

}
