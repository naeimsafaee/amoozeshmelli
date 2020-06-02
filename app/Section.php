<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Section extends Model{

    protected $fillable = [
        'title',
        'price',
        'gift_price',
        'early_price',
        'teacher_id',
        'subject_id',
        'quiz_id',
        'award',
        'helper_award',
        'pre_section_id',
        'opening_date',
        'can_pass',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected $appends = ["shamsi_opening_date"];

    public function getShamsiOpeningDateAttribute(){

        $date = $this->opening_date;
        $date = explode("-", $date);

        return gregorian_to_jalali($date[0], $date[1], $date[2], "/");
    }

}
