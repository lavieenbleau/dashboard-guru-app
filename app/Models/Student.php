<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'serial_id',
        'user_id',
        'classroom_id',
        'name',
        'username',
        'password',
        'role',
        'absen',
        'nis',
        'img',
        'address',
        'email',
        'phone',
        'login_at',
    ];

    public function serial()
    {
        return $this->belongsTo(Serial::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function exercisePoints()
    {
        return $this->hasMany(ExercisePoint::class);
    }
}