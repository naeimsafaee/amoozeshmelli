<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use MongoDB\Driver\Query;

class User extends Authenticatable{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'phone',
        'code',
        'grade_id',
        'city_id',
        'remember_token',
        'password',
        'fullName',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for arrays.
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast to native types.
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function grade_to_lessons(){
        return $this->hasMany(GradeToLesson::class, 'grade_id', 'grade_id');
    }

    public function teacher(){
        return $this->belongsTo(Teacher::class, 'id', 'user_id');
    }

    public function city(){
        return $this->hasOne(City::class, 'id', 'city_id');
    }

    public function grade(){
        return $this->hasOne(Grade::class, 'id', 'grade_id');
    }

    public function scopeTeacherOnly($query){

        return $query->whereIn('users.id' ,
            Teacher::query()->select("user_id"));
    }

}
