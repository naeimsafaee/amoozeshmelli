<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserToQuizzes extends Model{

    protected $fillable = ["user_id" , "quiz_id"];

}
