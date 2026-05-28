<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CsRoom extends Model
{
    protected $table = 'cs_rooms';

    protected $fillable = [
        'room_code',
        'question_categories_id',
        'student_id',
        'user_id',
        'admin_id',
        'chat_status',
    ];

    public function questionCategory()
    {
        return $this->belongsTo(QuestionCategory::class, 'question_categories_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function messages()
    {
        return $this->hasMany(CsMessage::class, 'cs_rooms_id');
    }

    public function files()
    {
        return $this->hasMany(CsFile::class, 'room_id');
    }
}