<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'address',
        'phone',
        'img',
        'login_at',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string,string>
     */
    protected $casts = [
        'password' => 'hashed',
        'login_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function serials()
    {
        return $this->hasMany(Serial::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function onlineMeetings()
    {
        return $this->hasMany(OnlineMeeting::class);
    }
}