<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnlineMeeting extends Model
{
    protected $fillable = [
        'serial_id',
        'classroom_id',
        'user_id',
        'mapel_id',
        'title',
        'description',
        'meeting_code',
        'meeting_link',
        'platform',
        'start_time',
        'end_time',
        'status',
        'room_id',
        'is_internal',
        'max_participants',
        'participants'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'participants' => 'array',
        'is_internal' => 'boolean',
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

    public function mapel()
    {
        return $this->belongsTo(Mapel::class);
    }

    // Generate unique meeting code
    public static function generateMeetingCode()
    {
        do {
            $code = 'MEET-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
        } while (self::where('meeting_code', $code)->exists());
        
        return $code;
    }

    // Check if meeting is active
    public function isActive()
    {
        $now = now();
        return $this->status === 'ongoing' || 
               ($this->status === 'scheduled' && $now->between($this->start_time, $this->end_time));
    }
}