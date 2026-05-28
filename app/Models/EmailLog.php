<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    protected $table = 'email_logs';

    protected $fillable = [
        'serial_id',
        'email_to',
        'subject',
        'email_type',
        'status',
        'source',
    ];

    public $timestamps = false;

    const CREATED_AT = 'created_at';

    public function serial()
    {
        return $this->belongsTo(Serial::class);
    }
}