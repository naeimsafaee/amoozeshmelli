<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quiz extends Model
{
//    use SoftDeletes;

    protected $fillable = ['title' , 'quiz_date' , 'quiz_time' , 'award' , 'price' , 'gift_price' , 'early_price' , 'answer_file'];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected $appends = ["shamsi_quiz_date"];

    public function getShamsiQuizDateAttribute(){

        $date = $this->quiz_date;
        $date = explode("-", $date);

        return gregorian_to_jalali($date[0], $date[1], $date[2], "/");
    }

    public function questions(){
        return $this->hasMany(Question::class, 'quiz_id', 'id');
    }

}
