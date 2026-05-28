<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $table = 'admins';

    protected $fillable = [
        'name',
        'username',
        'password',
        'role',
        'date_in',
        'position',
        'phone',
        'img',
        'login_at',
    ];

    protected $casts = [
        'date_in' => 'date',
        'login_at' => 'datetime',
    ];

    public function activityLogs()
    {
        return $this->hasMany(AdminActivityLog::class);
    }

    public function csRooms()
    {
        return $this->hasMany(CsRoom::class);
    }

    public function csLogs()
    {
        return $this->hasMany(CsLog::class);
    }
}