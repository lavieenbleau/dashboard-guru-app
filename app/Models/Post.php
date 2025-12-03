<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = "posts";

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
        'is_task',
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

    public function getCategoryDataAttribute()
    {
        return json_decode($this->category, true);
    }
}