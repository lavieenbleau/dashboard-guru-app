<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'serial_id',
        'student_id',
        'report',
        'img',
    ];

    public function serial()
    {
        return $this->belongsTo(Serial::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
