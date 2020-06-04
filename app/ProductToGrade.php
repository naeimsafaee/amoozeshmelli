<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductToGrade extends Model{

    protected $fillable = ["product_id", "grade_id"];

    protected $hidden = ["created_at", "updated_at"];

}
