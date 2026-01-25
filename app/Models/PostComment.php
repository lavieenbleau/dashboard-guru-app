<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostComment extends Model
{
    protected $fillable = [
        'post_id',
        'user_id',
        'student_id',
        'message',
        'code',
        'is_user'
    ];

    // Relationship to Post
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    // Relationship to User (Guru)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship to Student
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Relationship to child comments (replies)
    public function replies()
    {
        return $this->hasMany(PostChildComment::class, 'post_comment_id')->latest();
    }

    // Get commenter name
    public function getCommenterNameAttribute()
    {
        if ($this->is_user) {
            return $this->user ? $this->user->name : 'Unknown User';
        } else {
            return $this->student ? $this->student->name : 'Unknown Student';
        }
    }

    // Get commenter type
    public function getCommenterTypeAttribute()
    {
        return $this->is_user ? 'Guru' : 'Siswa';
    }
}
