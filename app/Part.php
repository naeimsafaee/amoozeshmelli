<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Part extends Model{

    protected $fillable = ["section_id", "video_id", 'question_id', 'order'];

    protected $hidden = ['created_at', 'updated_at'];

    public function question(){
        return $this->hasOne(Question::class, 'id', 'question_id');
    }

    public function video(){
        return $this->hasOne(Video::class, 'id', 'video_id');
    }

    public function section(){
        return $this->hasOne(Section::class, 'id', 'section_id');
    }

}
