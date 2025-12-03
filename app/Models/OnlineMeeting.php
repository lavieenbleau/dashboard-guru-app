<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnlineMeeting extends Model
{
    protected $fillable = [
        'serial_id', 'classroom_id', 'user_id',
        'title', 'description', 'meeting_code',
        'meeting_link', 'platform', 'start_time',
        'end_time', 'status'
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