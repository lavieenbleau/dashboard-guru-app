<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostChildComment extends Model
{
    protected $fillable = [
        'post_comment_id',
        'user_id',
        'student_id',
        'message',
        'is_user'
    ];

    // Relationship to parent comment
    public function parentComment()
    {
        return $this->belongsTo(PostComment::class, 'post_comment_id');
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
