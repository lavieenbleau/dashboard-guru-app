<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'serial_id', 'user_id', 'classroom_id', 'name', 'username',
        'password', 'password_text', 'nis', 'email', 'phone'
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
}