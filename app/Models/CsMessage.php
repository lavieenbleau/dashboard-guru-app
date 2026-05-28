<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CsMessage extends Model
{
    protected $table = 'cs_messages';

    protected $fillable = [
        'cs_rooms_id',
        'message_sender',
        'message_content',
        'sent_time',
    ];

    protected $casts = [
        'sent_time' => 'datetime',
    ];

    public function room()
    {
        return $this->belongsTo(CsRoom::class, 'cs_rooms_id');
    }
}