<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SerialLog extends Model
{
    protected $table = 'serial_logs';

    protected $fillable = [
        'serial_id',
        'active',
        'status',
    ];

    public function serial()
    {
        return $this->belongsTo(Serial::class);
    }
}