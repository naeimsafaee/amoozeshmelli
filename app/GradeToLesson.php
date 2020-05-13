<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GradeToLesson extends Model
{
    protected $hidden = ['created_at' , 'updated_at'];

    protected $fillable = ["grade_id" , 'lesson_id'];

    public function lessons(){
        return $this->belongsTo(Lesson::class , 'lesson_id');
    }
}
