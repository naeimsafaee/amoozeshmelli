<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TeacherToSubjects extends Model
{
    protected $fillable = ['teacher_id' , 'subject_id'];

    protected $hidden = ['created_at' , 'updated_at'];

    public function teacher(){
        return $this->hasOne(Teacher::class, 'id', 'teacher_id');
    }

}
