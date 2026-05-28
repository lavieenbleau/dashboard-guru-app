<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnlineMeetingParticipant extends Model
{
    protected $table = 'online_meeting_participants';

    protected $fillable = [
        'online_meeting_id',
        'user_id',
        'role',
        'joined_at',
        'left_at',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
    ];

    public function meeting()
    {
        return $this->belongsTo(OnlineMeeting::class, 'online_meeting_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}