<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model{

    protected $fillable = ["user_id" , "text" , "section_id" , "reply_to"];

    protected $hidden = ['created_at', 'updated_at'];

    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    public function reply_to(){
        return $this->hasOne(Comment::class, 'id', 'comment_id');
    }


}
