<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserToProducts extends Model{
    protected $fillable = ["user_id" , "product_id"];
}
