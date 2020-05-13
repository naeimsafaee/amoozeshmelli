<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Options extends Model{

    protected $fillable = ['title', 'image_id', 'question_id', 'is_correct'];
    protected $hidden = ['created_at', 'updated_at'];

    public function image(){
        return $this->hasOne(Image::class, 'id', 'image_id');
    }

    public function question(){
        return $this->hasOne(Question::class, 'id', 'question_id');
    }

}
