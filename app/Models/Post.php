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
        'user_id',
        'mapel_id',
        'title',
        'description',
        'slug',
        'link',
        'attachment',
        'embed',
        'category',
        'shared_to_classes',
        'deadline',
        'is_task',
    ];

    protected $casts = [
        'category' => 'array',
        'shared_to_classes' => 'array',
        'deadline' => 'datetime',
    ];

    public function serial()
    {
        return $this->belongsTo(Serial::class, 'serial_id');
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

    public function getCategoryDataAttribute()
    {
        return json_decode($this->category, true);
    }
}