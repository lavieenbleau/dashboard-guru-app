<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminActivityLog extends Model
{
    protected $table = 'admin_activity_logs';

    protected $fillable = [
        'admin_id',
        'action',
        'model',
        'data_id',
        'description',
        'ip_address',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}