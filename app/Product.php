<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model{

    protected $fillable = ["title", "image_id" , "price", "gift_price", "grade_id", "download_able", "file_path"];

    protected $hidden = ["created_at" , "updated_at" , "grade_id"];

    public function grade(){
        return $this->hasOne(Grade::class, 'id', 'grade_id');
    }

}
