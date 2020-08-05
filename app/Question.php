<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model{

    protected $fillable = ['title', 'image_id', 'quiz_id', 'lesson_id', 'subject_id', 'answer_file'];

    protected $hidden = ['created_at', 'updated_at',"quiz_id"  , "subject_id"];

    protected $appends = ["answer_file_url"];

    public function quiz(){
        return $this->hasOne(Quiz::class, 'id', 'quiz_id');
    }

    public function lesson(){
        return $this->hasOne(Lesson::class, 'id', 'lesson_id');
    }

    public function subject(){
        return $this->hasOne(Subject::class, 'id', 'subject_id');
    }

    public function image(){
        return $this->hasOne(Image::class, 'id', 'image_id');
    }

    public function options(){
        return $this->hasMany(Options::class, 'question_id', 'id');
    }

    public function getAnswerFileUrlAttribute(){
        if($this->answer_file == null)
            return "null";
        return $this->answer_file;
    }

}
