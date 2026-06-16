<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;

    protected $table = 'posts';

    protected $fillable = [
        'serial_id',
        'classroom_id',
        'user_id',
        'mapel_id',
        'title',
        'description',
        'slug',
        'link',
        'attachment',
        'embed',
        'due_date',
        'category',
        'is_task',
    ];

    protected $casts = [
        'category' => 'array',
        'due_date' => 'datetime',
    ];

    public function serial()
    {
        return $this->belongsTo(Serial::class, 'serial_id');
    }
    
    public function classroom()
    {
        return $this->belongsTo(Classroom::class, 'classroom_id');
    }

    public function classrooms()
    {
        return $this->belongsToMany(Classroom::class, 'post_classrooms', 'post_id', 'classroom_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }

    public function comments()
    {
        return $this->hasMany(PostComment::class)->latest();
    }

    public function getDeadlineAttribute()
    {
        return $this->due_date;
    }

    public function setDeadlineAttribute($value)
    {
        $this->attributes['due_date'] = $value;
    }

    public function getSharedToClassesAttribute()
    {
        return null;
    }
}
